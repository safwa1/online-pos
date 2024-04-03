<?php

namespace App\Filament\Resources\SupplierResource\RelationManagers;

use App\Models\Currency;
use App\Models\Entry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Components\Tab;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'entries';

    protected static ?string $label = "قيد";
    protected static ?string $title = "القيود";

    protected static bool $isLazy = false;

    public function getTabs(): array
    {
        $tabs = [];
        $currencies = Currency::all();

        foreach ($currencies as $currency) {
            $tabs[$currency->name] = Tab::make($currency->name)
                ->label($currency->name)
                ->badge(Entry::query()->where('currency_id', $currency->id)->count())
                ->modifyQueryUsing(function ($query) use ($currency) {
                    return $query->where('currency_id', $currency->id);
                });
        }

        return $tabs;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('الحساب والعملة')
                    ->schema([
                        Forms\Components\Select::make('account_type')
                            ->label('نوع الحساب')
                            ->native(false)
                            ->options([
                                'customer' => 'عميل',
                            ])
                            ->default('customer')
                            ->required(),
                        Forms\Components\Select::make('currency_id')
                            ->label('العملة')
                            ->relationship('currency', 'name')
                            ->native(false)
                            ->searchable()
                            ->required()
                            ->preload()
                            ->live()
                            ->default(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Textarea::make('statement')
                            ->label('البيان')
                            ->required()
                            ->maxLength(255)
                            ->live()
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText(function (Forms\Get $get) {
                                $state = str($get('statement'))->length();
                                return $state . '/255';
                            }),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\TextInput::make('creditor')
                                    ->label('مبلغ الدائن (له)')
                                    ->suffixActions(self::suffixInputActions('creditor'))
                                    ->prefixActions(self::prefixInputActions('creditor'))
                                    ->required()
                                    ->regex("([0-9]+)")
                                    ->minValue(0)
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                        $creditor = doubleval($state ?? '0');
                                        $debtor = doubleval($get('debtor') ?? '0');
                                        $set('balance', $debtor <= 0 ? abs($creditor - $debtor) * -1 : abs($creditor - $debtor));
                                    }),
                                Forms\Components\TextInput::make('debtor')
                                    ->label('مبلغ المدين (عليه)')
                                    ->suffixActions(self::suffixInputActions('debtor'))
                                    ->prefixActions(self::prefixInputActions('debtor'))
                                    ->required()
                                    ->regex("([0-9]+)")
                                    ->minValue(0)
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                        $creditor = doubleval($get('creditor') ?? '0');
                                        $debtor = doubleval($state ?? '0');
                                        $set('balance', abs($creditor - $debtor));
                                    }),
                                Forms\Components\TextInput::make('balance')
                                    ->label('الرصيد')
                                    ->required()
                                    ->live()
                                    ->minValue(0)
                                    ->default(0)
                                    ->columnSpan(1)
                                    ->readOnly()
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('document')
                            ->label('المستند (المرجع)')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('document_number')
                            ->label('رقم المستند (المرجع)')
                            ->maxLength(100),
                        Forms\Components\DateTimePicker::make('date')
                            ->native(false)
                            ->placeholder('dd/MM/yyyy')
                            ->displayFormat('d/m/Y')
                            ->label('التاريخ')
                            ->maxDate(now()->addDay())
                            ->default(now()->addDay())
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('account_id')
            ->groups([
                Group::make('user.name')->label('المستخدم')->collapsible(),
                Group::make('currency.name')->label('العملة')->collapsible(),
                Group::make('date')->label('التاريخ')->collapsible(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency.name')
                    ->label('العملة')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('creditor')
                    ->sortable()
                    ->summarize([
                        Sum::make()->numeric(
                            decimalPlaces: 2,
                            decimalSeparator: '.',
                            thousandsSeparator: ',',
                        ),
                    ])
                    ->label('دائن'),
                Tables\Columns\TextColumn::make('debtor')
                    ->summarize([
                        Sum::make()->numeric(
                            decimalPlaces: 2,
                            decimalSeparator: '.',
                            thousandsSeparator: ',',
                        ),
                    ])
                    ->sortable()
                    ->label('مدين'),
                Tables\Columns\TextColumn::make('balance')
                    ->label('الرصيد')
                    ->summarize([
                        Sum::make()->numeric(
                            decimalPlaces: 2,
                            decimalSeparator: '.',
                            thousandsSeparator: ',',
                        ),
                    ]),
                Tables\Columns\TextColumn::make('statement')
                    ->label('البيان')
                    ->limit(30)
                    ->wrap()
                    ->tooltip(fn($record): string => $record->statement)
                    ->searchable(),

                Tables\Columns\TextColumn::make('date')
                    ->label('التاريخ')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('document')
                    ->label('المستند')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('document_number')
                    ->label('رقمه')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاريخ التحديث')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
