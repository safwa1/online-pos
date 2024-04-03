<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Supplier;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = "الحسابات";

    protected static ?string $modelLabel = 'مورد';

    protected static ?string $pluralLabel = 'الموردين';

    protected static ?string $navigationLabel = 'إدارة الموردين';

    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->collapsible()
                    ->schema([
                    Forms\Components\TextInput::make('name')
                        ->maxLength(255)
                        ->label('اسم المورد')
                        ->prefixIcon('heroicon-o-user')
                        ->unique(Supplier::class)
                        ->required(),
                    Forms\Components\TextInput::make('company_name')
                        ->label('اسم الشركة')
                        ->maxLength(255)
                        ->prefixIcon('heroicon-o-home-modern'),
                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->label('رقم الهاتف')
                        ->prefixIcon('heroicon-o-phone')
                        ->maxLength(30),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->prefixIcon('heroicon-o-at-symbol')
                        ->label('البريد الإلكتروني')
                        ->maxLength(50),
                    Forms\Components\TextInput::make('limit')
                        ->numeric()
                        ->prefixIcon('heroicon-o-bell-alert')
                        ->label('سقف الحساب')
                        ->helperText('أقصى مديونية مسموحة لهذا الحساب.')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('address')
                        ->rows(4)
                        ->label('العنوان')
                        ->hintIcon('heroicon-o-map-pin')
                        ->maxLength(255)
                        ->columnSpanFull(),
                ])->columns(2),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->label('حساب نشط')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable()
                    ->label('المستخدم'),
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم المورد')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->label('العنوان')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('limit')
                    ->label('سقف الحساب')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('حساب نشط'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('تاريخ الإنشاء')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('تاريخ التحديث')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('supplier_report')
                        ->label('كشف حساب')
                        ->icon('heroicon-o-document-arrow-down')
                        ->form([
                            Forms\Components\Group::make()
                                ->schema([
                                    Forms\Components\Select::make('currency')
                                        ->label('العملة')
                                        ->searchable()
                                        ->native(false)
                                        ->preload()
                                        ->options(Currency::all()->pluck('name', 'id'))
                                        ->default(1)
                                        ->required(),
                                    Forms\Components\Section::make()
                                        ->schema([
                                            Forms\Components\DatePicker::make('startDate')->label('من')
                                                ->native(false)
                                                ->placeholder('dd/MM/yyyy')
                                                ->displayFormat('d/m/Y')
                                                ->maxDate(now()->addDay()),
                                            Forms\Components\DatePicker::make('endDate')->label('إلى')
                                                ->native(false)
                                                ->placeholder('dd/MM/yyyy')
                                                ->displayFormat('d/m/Y')
                                                ->maxDate(now()->addDay())
                                        ])->columns(2)
                                ])->columns(1)
                        ])
                        ->action(function (Supplier $record, array $data) {
                            $startDate = $data['startDate'] ?? "null";
                            $endDate = $data['endDate'] ?? "null";
                            $currency = $data['currency'] ?? "null";
                            redirect()->route('supplier.report', [
                                'id' => $record->id,
                                'currency' => $currency,
                                'startDate' => $startDate,
                                'endDate' => $endDate,
                            ]);
                        }),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\EntriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            //'view' => Pages\ViewSupplier::route('/{record}'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
