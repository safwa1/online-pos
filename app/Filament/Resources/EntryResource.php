<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntryResource\Pages;
use App\Filament\Resources\EntryResource\RelationManagers;
use App\Models\Customer;
use App\Models\Delegate;
use App\Models\Entry;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class EntryResource extends Resource
{
    protected static ?string $model = Entry::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationGroup = "الحسابات";

    protected static ?string $modelLabel = 'قيد';

    protected static ?string $pluralLabel = 'القيود';

    protected static ?string $navigationLabel = 'إدارة القيود';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('الحساب والعملة')
                    ->schema([
                        Forms\Components\Select::make('account_type')
                            ->label('نوع الحساب')
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->options([
                                'customer' => 'عميل',
                                'supplier' => 'مورد',
                                'delegate' => 'مندوب',
                            ])
                            ->live()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('account_id')
                            ->label('الحساب')
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->reactive()
                            ->options(function (Forms\Get $get) {
                                $types = ['customer' => 'customer', 'supplier' => 'supplier', 'delegate' => 'delegate'];
                                $state = $get('account_type');
                                if (!$state) return [];
                                $type = $types[$state];
                                return match ($type) {
                                    'customer' => Customer::all()->pluck('name', 'id')->toArray(),
                                    'supplier' => Supplier::all()->pluck('name', 'id')->toArray(),
                                    'delegate' => Delegate::all()->pluck('name', 'id')->toArray(),
                                };
                            }),
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
                        Forms\Components\TextInput::make('creditor')
                            ->label('مبلغ الدائن (له)')
                            ->suffixActions(self::suffixInputActions('creditor'))
                            ->prefixActions(self::prefixInputActions('creditor'))
                            ->required()
                            ->regex("([0-9]+)")
                            ->minValue(0)
                            ->default(0),
                        Forms\Components\TextInput::make('debtor')
                            ->label('مبلغ المدين (عليه)')
                            ->suffixActions(self::suffixInputActions('debtor'))
                            ->prefixActions(self::prefixInputActions('debtor'))
                            ->required()
                            ->live()
                            ->minValue(0)
                            ->default(0),
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

    public static function table(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('user.name')->label('المستخدم')->collapsible(),
                Group::make('account_type')->label('نوع الحساب')->collapsible(),
                Group::make('account_id')->label('الحساب')->collapsible(),
                Group::make('currency.name')->label('العملة')->collapsible(),
                Group::make('date')->label('التاريخ')->collapsible(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('account_type')
                    ->label('نوع الحساب')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function (Model $record): string {
                        $data = $record->toArray();
                        $types = [
                            'customer' => 'عميل',
                            'supplier' => 'مورد',
                            'delegate' => 'مندوب',
                        ];
                        return $types[$data['account_type']];
                    }),
                Tables\Columns\TextColumn::make('account_id')
                    ->label('الحساب')
                    ->limit(20)
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function (Model $record): string {
                        $data = $record->toArray();
                        return $record->getAccount($data['account_id'], $data['account_type']);
                    }),
                Tables\Columns\TextColumn::make('currency.name')
                    ->label('العملة')
                    ->numeric()
                    ->searchable()
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
                SelectFilter::make('currency')
                    ->label('حسب العملة')
                    ->relationship('currency', 'name')
                    ->default(1)
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                ]) ->link()
                    ->label('خيارات')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEntries::route('/'),
            'create' => Pages\CreateEntry::route('/create'),
            'view' => Pages\ViewEntry::route('/{record}'),
            'edit' => Pages\EditEntry::route('/{record}/edit'),
        ];
    }

    private static function suffixInputActions($name): array
    {
        return [
            Action::make('plus')
                ->icon('heroicon-o-chevron-up')
                ->hidden(function (string $operation) {
                    return $operation == 'view';
                })
                ->action(function (Forms\Get $get, Set $set) use ($name) {
                    $set($name, intval($get($name)) + 1);
                }),
            Action::make('minus')
                ->icon('heroicon-o-chevron-down')
                ->hidden(function (string $operation) {
                    return $operation == 'view';
                })
                ->action(function (Forms\Get $get, Set $set) use ($name) {
                    $current = intval($get($name));
                    $set($name, $current > 0 ? $current - 1 : 0);
                }),

        ];
    }

    private static function prefixInputActions($name): array
    {
        return [
            Action::make('clear')
                ->hidden(function (string $operation) {
                    return $operation == 'view';
                })
                ->icon('heroicon-o-x-mark')
                ->action(function (Forms\Get $get, Set $set) use ($name) {
                    $set($name, '');
                }),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}

