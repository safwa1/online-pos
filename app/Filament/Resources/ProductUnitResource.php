<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductUnitResource\Pages;
use App\Filament\Resources\ProductUnitResource\RelationManagers;
use App\Models\ProductUnit;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductUnitResource extends Resource
{
    protected static ?string $model = ProductUnit::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';

    protected static ?string $navigationGroup = "إدارة المخزون";

    protected static ?string $modelLabel = 'وحدة منتج';

    protected static ?string $pluralLabel = 'وحدات المنتجات';

    protected static ?string $navigationLabel = 'إدارة وحدات المنتجات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الوحدة')->schema([
                    Forms\Components\Select::make('product_id')
                        ->relationship('product', 'name')
                        ->required()
                        ->live()
                        ->native(false)
                        ->searchable()
                        ->preload()
                        ->label('إسم المنتج'),
                    Forms\Components\Select::make('unit_id')
                        ->relationship('unit', 'name')
                        ->required()
                        ->live()
                        ->native(false)
                        ->searchable()
                        ->preload()
                        ->label('إسم الوحدة'),
                    Forms\Components\TextInput::make('count')
                        ->required()
                        ->numeric()
                        ->label('السعة'),
                    Forms\Components\TextInput::make('barCode')
                        ->maxLength(50)
                        ->label('الباركود')
                        ->suffixAction(fn() => Action::make('generate')->icon('heroicon-o-sparkles')->action(function (Set $set) {
                            $barcodeNumber = Carbon::now()->format('YmdHis');
                            //$barcodeNumber .= mt_rand(1000, 9999);
                            $set('barCode', $barcodeNumber);

                        })),
                ])->columns(4),

                Forms\Components\Section::make('سعر الوحدة')->schema([
                    Forms\Components\TextInput::make('purchasePrice')
                        ->required()
                        ->numeric()
                        ->label('سعر الشراء'),
                    Forms\Components\Section::make('سعر التجزية')->schema([
                        Forms\Components\TextInput::make('retailSalePrice')
                            ->required()
                            ->numeric()
                            ->label('سعر بيع التجزئة'),
                        Forms\Components\TextInput::make('minRetailSalePrice')
                            ->required()
                            ->numeric()
                            ->label('أقل سعر بيع تجزئة')
                    ])->columns(2),
                    Forms\Components\Section::make('سعر الحملة')->schema([
                        Forms\Components\TextInput::make('wholesaleSalePrice')
                            ->required()
                            ->numeric()
                            ->label('سعر بيع الحملة'),
                        Forms\Components\TextInput::make('minWholesaleSalePrice')
                            ->required()
                            ->numeric()
                            ->label('أقل سعر بيع جملة')
                    ])->columns(2)
                ]),
                Forms\Components\Toggle::make('isMainUnit')
                    ->label('وحدة إفتراضية')
                    ->required()
                    ->hidden(function (Forms\Get $get, $operation) {
                        $isCreate = $operation == 'create';
                        $isEdit = $operation == 'edit';
                        $hasDefaultUnit = ProductUnit::where('isMainUnit', true)->exists();
                        if ($isEdit) {
                            $id = $get('id');
                            $isDefault = ProductUnit::find($id)->isMainUnit == 1;
                            if ($isDefault) return false;
                            else return $hasDefaultUnit;
                        }

                        return $isCreate && $hasDefaultUnit;
                    })
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('product.name')
            ->groups([
                Group::make('product.name')->label('المنتج')->collapsible(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('unit.name')
                    ->label('إسم الوحدة')
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('إسم المنتج')
                    ->sortable()
                    ->hidden(),
                Tables\Columns\TextColumn::make('count')
                    ->label('السعة')
                    ->sortable(),
                Tables\Columns\TextColumn::make('barCode')
                    ->label('الباركود')
                    ->searchable(),
                Tables\Columns\TextColumn::make('purchasePrice')
                    ->label('سعر الشراء')
                    ->sortable(),
                Tables\Columns\TextColumn::make('retailSalePrice')
                    ->label('سعر البيع بالتجزئة')
                    ->sortable(),
                Tables\Columns\TextColumn::make('minRetailSalePrice')
                    ->sortable()
                    ->label('أقل سعر بيع بالتجزئة'),
                Tables\Columns\TextColumn::make('wholesaleSalePrice')
                    ->label('سعر البيع بالجملة')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('minWholesaleSalePrice')
                    ->sortable()
                    ->label('أقل سعر بيع بالجملة')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('isMainUnit')
                    ->label('وحدة إفتراضية')
                    ->boolean(),
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
//                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductUnits::route('/'),
            'create' => Pages\CreateProductUnit::route('/create'),
            'view' => Pages\ViewProductUnit::route('/{record}'),
            'edit' => Pages\EditProductUnit::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

}
