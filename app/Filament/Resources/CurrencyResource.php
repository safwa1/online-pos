<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Filament\Resources\CurrencyResource\RelationManagers;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = "إعدادات النظام";

    protected static ?string $modelLabel = 'عملة';

    protected static ?string $pluralLabel = 'العملات';

    protected static ?string $navigationLabel = 'إدارة العملات';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('المعلومات الأساسية')->schema([

                    Forms\Components\Select::make('code')
                        ->label('الكود')
                        ->required()
                        ->options([
                            'YER',
                            'SAR',
                            'USD',
                        ])
                        ->native(false)
                        ->live()
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('name', $state);
                        $set('country', $state);
                        $set('smallest_unit_name', $state);
                    }),

                    Forms\Components\Select::make('name')
                        ->label('الاسم')
                        ->options([
                            'ريال بمني',
                            'ريال سعودي',
                            'دولار أمريكي'
                        ])
                        ->native(false)
                        ->live()
                        ->required()
                        ->afterStateUpdated(function (Set $set, $state) {
                            $set('country', $state);
                        }),

                    Forms\Components\Select::make('country')
                        ->label('البلد')
                        ->options([
                            'اليمن',
                            'السعودية',
                            'الولايات المتحدة الأمريكية'
                        ])
                        ->native(false)
                        ->live(),

                    Forms\Components\Select::make('symbol')
                        ->label('الرمز')
                        ->options([
                            '﷼',
                            '$',
                            '€',
                            '¥',
                            '£',
                        ])
                        ->native(false)
                        ->live(),
                ])->columns(2),

                Forms\Components\Section::make('السعر')
                    ->schema([
                        Forms\Components\TextInput::make('decimal_places')
                            ->label('المنازل العشرية')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10),
                        Forms\Components\TextInput::make('exchange_rate')
                            ->label('سعر الصرف')
                            ->required()
                            ->numeric()
                    ])->columns(2)->columnSpan(1),

                Forms\Components\Section::make('الوحدة الصغرى')
                    ->schema([
                        Forms\Components\TextInput::make('smallest_unit_rate')
                            ->label('سعر الوحدة الصغرى')
                            ->numeric(),
                        Forms\Components\Select::make('smallest_unit_name')
                            ->label('اسم الوحدة الصغرى')
                            ->options([
                                'فلس',
                                'هللة',
                                'سنت',
                            ])
                            ->native(false)
                            ->live(),
                    ])->columns(2)->columnSpan(1),
                Forms\Components\Toggle::make('is_default')
                    ->label('تعيين كعملة إفتراضية')
                    ->hidden(function (Get $get, $operation) {
                        $isCreate = $operation == 'create';
                        $isEdit = $operation == 'edit';
                        $hasDefaultCurrency = Currency::where('is_default', '=', true)->exists();
                        if($isEdit) {
                            $id = $get('id');
                            $isDefault = Currency::find($id)->is_default == 1;
                            if($isDefault) return false;
                            else return $hasDefaultCurrency;
                        }

                        return $isCreate && $hasDefaultCurrency;
                    })
                    ->live()
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('الكود')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('country')
                    ->label('البلد')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('symbol')
                    ->label('الرمز')
                    ->searchable(),
                Tables\Columns\TextColumn::make('decimal_places')
                    ->label('المنازل العشرية')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('exchange_rate')
                    ->label('سعر الصرف')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('smallest_unit_rate')
                    ->label('سعر الوحدة الصغرى')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('smallest_unit_name')
                    ->label('اسم الوحدة الصغرى')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_default')
                    ->label('الإفتراضية')
                    ->boolean(),
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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->recordUrl(
                fn(Model $record): string => Pages\ViewCurrency::getUrl([$record->id]),
            )
            ->recordAction(Tables\Actions\ViewAction::class);
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
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'view' => Pages\ViewCurrency::route('/{record}'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }

}
