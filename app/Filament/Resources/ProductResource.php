<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers\UnitsRelationManager;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Store;
use App\Models\Unit;
use Carbon\Carbon;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = "إدارة المخزون";

    protected static ?string $modelLabel = 'منتج';

    protected static ?string $pluralLabel = 'المنتجات';

    protected static ?string $navigationLabel = 'إدارة المنتجات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Group::make()
                    ->schema([

                        Forms\Components\Section::make('التصنيف، المزود')
                            ->schema([

                                Forms\Components\Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->label('التصنيف')
                                    ->native(false)
                                    ->preload()
                                    ->live()
                                    ->searchable()
                                    ->createOptionForm(self::createCategoryFrom()),

                                Forms\Components\Select::make('provider_id')
                                    ->relationship('provider', 'name')
                                    ->native(false)
                                    ->label('المزود (الموزع)')
                                    ->preload()
                                    ->live()
                                    ->searchable()
                                    ->createOptionForm(self::createProviderForm()),

                            ])->columns(2),

                        Forms\Components\Section::make('المعلومات الأساسية')
                            ->schema([

                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->label('إسم المنتج')
                                    ->maxLength(255)
                                    ->live(),

                                Forms\Components\TextInput::make('barCode')
                                    ->label('الباركود')
                                    ->maxLength(50)
                                    ->suffixAction(fn() => Action::make('generate')->icon('heroicon-o-sparkles')->action(function (Set $set) {
                                        $barcodeNumber = Carbon::now()->format('YmdHis');
                                        //$barcodeNumber .= mt_rand(1000, 9999);
                                        $set('barCode', $barcodeNumber);

                                    })),

                                Forms\Components\Textarea::make('description')
                                    ->label('الوصف')
                                    ->rows(4)
                                    ->maxLength(255)->columnSpanFull(),

                            ])->columns(2),

                        Forms\Components\Section::make('وحدات الصنف')
                            ->schema([

                                Forms\Components\Group::make()->schema([
                                    Forms\Components\Select::make('product_unit_id')
                                        ->label('الوحدة الأساسية')
                                        ->required()
                                        ->native(false)
                                        ->searchable()
                                        ->live()
                                        ->statePath('product_unit_id')
                                        ->disabled(function (string $operation) {
                                            return $operation == 'edit';
                                        })
                                        ->options(function (Get $get, string $operation) {
                                            if ($operation == 'create')
                                                return Unit::all()->pluck('name', 'id');
                                            else
                                                return ProductUnit::with('unit:id,name')
                                                    ->where('product_id', $get('id'))
                                                    ->get()
                                                    ->pluck('unit.name', 'id');
                                        }),

                                    Forms\Components\TextInput::make('quantity')
                                        ->numeric()
                                        ->label('الكمية الإفتتاحية'),

                                    Forms\Components\Select::make('store')
                                        ->label('المخزن')
                                        ->native(false)
                                        ->searchable()
                                        ->live()
                                        ->preload()
                                        ->options(Store::all()->pluck('name', 'id'))
                                        ->createOptionForm(self::createStoreForm())
                                        ->createOptionUsing(function (array $data): int {
                                            return Store::query()->create($data)->getKey();
                                        })
                                ])->columns(3),

                                Forms\Components\Builder::make('sub_units')
                                    ->hidden(function (Forms\Get $get, string $operation) {
                                        return !$get('product_unit_id') || $operation == 'edit';
                                    })
                                    ->live()
                                    ->label('الوحدات الفرعية')
                                    ->blocks([
                                        Forms\Components\Builder\Block::make('new_unit')
                                            ->label('وحدة جديدة')
                                            ->schema([

                                                Forms\Components\Group::make()
                                                    ->schema([
                                                        Forms\Components\Select::make('unit_id')
                                                            ->label('اسم الوحدة')
                                                            ->native(false)
                                                            ->searchable()
                                                            ->options(Unit::all()->pluck('name', 'id')),

                                                        Forms\Components\TextInput::make('count')
                                                            ->label('معامل التحويل')
                                                            ->numeric()
                                                            ->live()
                                                            ->helperText(function (Set $set, Get $get, $state) {
                                                                return 'الـ 1 من و.أ يحتوي على ' . $get('count') . ' وحدة.';
                                                            }),

                                                        Forms\Components\TextInput::make('barCode')
                                                            ->maxLength(50)
                                                            ->label('الباركود')
                                                            ->suffixAction(fn() => Action::make('generate')
                                                                ->icon('heroicon-o-sparkles')
                                                                ->action(function (Set $set) {
                                                                    $barcodeNumber = Carbon::now()->format('YmdHis');
                                                                    $set('barCode', $barcodeNumber);
                                                                })
                                                            ),

                                                    ])->columns(3)->columnSpanFull(),

                                                Forms\Components\Group::make()->schema([
                                                    Forms\Components\TextInput::make('quantity')
                                                        ->numeric()
                                                        ->label('الكمية الإفتتاحية'),

                                                ])->columns(1),

                                                Forms\Components\Section::make('السعر')
                                                    ->schema([

                                                        Forms\Components\TextInput::make('purchasePrice')
                                                            ->required()
                                                            ->numeric()
                                                            ->label('سعر الشراء')
                                                            ->live(),
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

                                                    ])->columnSpanFull()
                                            ])
                                    ])
                            ]),

                        Forms\Components\Section::make()->schema([
                            Forms\Components\TextInput::make('minQuantity')
                                ->label('حد الطلب')
                                ->numeric()
                                ->default(0)
                        ])

                    ])->columnSpan([
                        'sm' => 'full',
                        'md' => 'full',
                        'lg' => 'full',
                        'xl' => 2,
                        '2xl' => 2,
                    ]),

                Forms\Components\Section::make('سعر الوحدة الأساسية')
                    ->schema([
                        Forms\Components\TextInput::make('purchasePrice')
                            ->required()
                            ->numeric()
                            ->live()
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
                        ]),
                        Forms\Components\Section::make('سعر الحملة')->schema([
                            Forms\Components\TextInput::make('wholesaleSalePrice')
                                ->required()
                                ->numeric()
                                ->label('سعر بيع الحملة'),
                            Forms\Components\TextInput::make('minWholesaleSalePrice')
                                ->required()
                                ->numeric()
                                ->label('أقل سعر بيع جملة')
                        ]),
                    ])->columnSpan([
                        'sm' => 'full',
                        'md' => 'full',
                        'lg' => 'full',
                        'xl' => 1,
                        '2xl' => 1,
                    ])


            ])->columns([
                'sm' => 1,
                'md' => 1,
                'lg' => 1,
                'xl' => 3,
                '2xl' => 3,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->groupingSettingsHidden(false)
            ->groups([
                Group::make('user.name')->label('المالك')->collapsible(),
                Group::make('category.name')->label('التصنيف')->collapsible(),
                Group::make('provider.name')->label('المزود (الموزع)')->collapsible(),
                Group::make('name')->label('إسم المنتج')->collapsible(),
                Group::make('created_at')->label('تاريخ الإضافة'),
                Group::make('updated_at')->label('تاريخ التعديل'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('المالك')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('التصنيف')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('provider.name')
                    ->label('المزود (الموزع)')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('productUnit.unit.name')
                    ->label('الوحدة الإفتراضية')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('إسم المنتج'),
                Tables\Columns\TextColumn::make('barCode')
                    ->label('الباركود')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('الوصف')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('purchasePrice')
                    ->sortable()
                    ->label('سعر الشراء'),
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
            UnitsRelationManager::class
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    private static function createStoreForm(): array
    {
        return [
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('إسم المخزن'),
                Forms\Components\TextInput::make('location')
                    ->label('موقع المخزن')
                    ->maxLength(255),
                Forms\Components\TextInput::make('manager_name')
                    ->label('إسم المسؤول')
                    ->maxLength(255),
                Forms\Components\TextInput::make('manager_phone')
                    ->label('رقم هاتف المسؤول')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('manager_email')
                    ->label('بريده الإلكترني')
                    ->email()
                    ->maxLength(255),
        ];
    }

    private static function createCategoryFrom(): array
    {
        return [
            Forms\Components\Select::make('category_id')
                ->label('تصنيف فرعي من')
                ->relationship('category', 'name')
                ->native(false)
                ->searchable()
                ->preload()
                ->columnSpanFull(),
            Forms\Components\TextInput::make('name')
                ->label('الاسم')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('hierarchy')
                ->label('التسلسل')
                ->maxLength(255),
        ];
    }

    private static function createProviderForm(): array
    {
        return [
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')
                    ->label('الاسم')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('company_name')
                    ->label('إسم الشركة')
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('رقم الهاتف')
                    ->tel()
                    ->suffixIcon('heroicon-o-phone')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->suffixIcon('heroicon-o-at-symbol')
                    ->maxLength(255),
                Forms\Components\Textarea::make('address')
                    ->label('العنوان')
                    ->maxLength(255)->columnSpanFull(),
            ])->columns(2)
        ];
    }
}
