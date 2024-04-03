<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Unit;
use Carbon\Carbon;
use Filament\Actions\CreateAction;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Termwind\Components\Element;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $title = 'المنتجات';

    protected static ?string $label = "منتج";

    protected static ?string $modelLabel = "منتج";

    protected static ?string $pluralLabel = "منتج";
    protected static ?string $pluralModelLabel = "منتجات";
    protected static bool $isLazy = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Group::make()
                    ->schema([

                        Forms\Components\Select::make('provider_id')
                            ->relationship('provider', 'name')
                            ->native(false)
                            ->label('المزود (الموزع)')
                            ->preload()
                            ->live()
                            ->searchable()
                            ->createOptionForm(self::createProviderForm()),

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

                        Forms\Components\Section::make('السعر')
                            ->schema([
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
                                ])->columns(2),

                            ]),

                        Forms\Components\TextInput::make('minQuantity')
                            ->label('حد الطلب')
                            ->numeric()
                            ->default(0)
                            ->columnSpanFull(),

                        Forms\Components\Section::make('وحدات الصنف')
                            ->schema([

                                Forms\Components\Select::make('product_unit_id')
                                    ->label('الوحدة الأساسية')
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

                                                Forms\Components\Section::make('السعر')
                                                    ->schema([

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
                                                        ])->columns(2),

                                                    ])->columnSpanFull()
                                            ])
                                    ])
                            ])

                    ])->columnSpan(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('user.name')->label('المالك'),
                Group::make('category.name')->label('التصنيف'),
                Group::make('provider.name')->label('المزود (الموزع)'),
                Group::make('name')->label('إسم المنتج'),
                Group::make('created_at')->label('تاريخ الإضافة'),
                Group::make('updated_at')->label('تاريخ التعديل'),
            ])
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('المالك')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('provider.name')
                    ->label('المزود (الموزع)')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('productUnit.unit.name')
                    ->label('الوحدة الإفتراضية')
                    ->numeric()
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
                    ->numeric()
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
                    ->label('تاريخ التحديث')
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
