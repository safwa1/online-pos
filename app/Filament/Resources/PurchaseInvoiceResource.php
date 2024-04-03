<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseInvoiceResource\Pages;
use App\Filament\Resources\PurchaseInvoiceResource\RelationManagers;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\PurchaseInvoice;
use App\Models\Store;
use Filament\Forms;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use LaraZeus\Quantity\Components\Quantity;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;

class PurchaseInvoiceResource extends Resource
{
    protected static ?string $model = PurchaseInvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = "الفواتير";

    protected static ?string $modelLabel = 'فاتورة';

    protected static ?string $pluralLabel = 'فواتير المشتريات';

    protected static ?string $navigationLabel = 'فواتير المشتريات';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                self::wizard()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('supplier.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPurchaseInvoices::route('/'),
            'create' => Pages\CreatePurchaseInvoice::route('/create'),
            'view' => Pages\ViewPurchaseInvoice::route('/{record}'),
            'edit' => Pages\EditPurchaseInvoice::route('/{record}/edit'),
        ];
    }

    private static function wizard(): Forms\Components\Wizard
    {
        return Forms\Components\Wizard::make([
            self::firstStep(),
            self::secondStep(),
            self::thirdStep()
        ])->columnSpanFull()->live();
    }

    private static function firstStep(): Forms\Components\Wizard\Step
    {
        return Forms\Components\Wizard\Step::make('البيانات الاساسية')
            ->schema([
                Section::make('البيانات الاساسية')
                    ->collapsible()
                    ->schema([

                        Forms\Components\Group::make()
                            ->schema([
                                TextInput::make('number')
                                    ->label('رقم الفاتورة')
                                    ->placeholder('إختياري'),

                                Forms\Components\Select::make('supplier_id')
                                    ->relationship('supplier', 'name')
                                    ->searchable()
                                    ->label('المورد')
                                    ->preload()
                                    ->live()
                                    ->native(false),

                                Select::make('invoice_type')
                                    ->label('نوع الفاتورة')
                                    ->searchable()
                                    ->native(false)
                                    ->required()
                                    ->options([
                                        'نقداً' => 'نقداً',
                                        'آجل' => 'آجل'
                                    ])
                                    ->default('نقداً'),

                                Forms\Components\Select::make('store')
                                    ->label('المخزن')
                                    ->native(false)
                                    ->searchable()
                                    ->live()
                                    ->preload()
                                    ->required()
                                    ->options(Store::all()->pluck('name', 'id'))
                                    ->createOptionForm(self::createStoreForm())
                                    ->createOptionUsing(function (array $data): int {
                                        return Store::query()->create($data)->getKey();
                                    })
                            ])->columns(4),
                    ]),
            ]);
    }

    private static function secondStep(): Forms\Components\Wizard\Step
    {
        return Forms\Components\Wizard\Step::make('إضافة المنتجات')
            ->schema([
                Builder::make('products')
                    ->live()
                    ->label('المنتجات')
                    ->blocks([
                        Block::make('new_product')
                            ->label('منتج جديد')
                            ->schema([

                                Group::make()
                                    ->schema([
                                        Select::make('product')
                                            ->label('حدد منتج')
                                            ->required()
                                            ->live()
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->options(Product::all()->pluck('name', 'id'))
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                $product = Product::query()->firstWhere('id', $state);
                                                //$price = Item::query()->where('product_id', $state)->latest()->value('purchasePrice');
                                                $set('quantity', 1);
                                                $set('unit_price', $product->purchasePrice ?? 0);
                                                $set('total', ($product->purchasePrice ?? 0) * 1);
                                                $set('unit', ProductUnit::with('unit:id,name')
                                                    ->firstWhere('isMainUnit', true)
                                                    ->value('id'));
                                            })
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('product')->label('إسم المنتج')
                                            ]),

                                        Select::make('unit')
                                            ->searchable()
                                            ->required()
                                            ->preload()
                                            ->options(function (Forms\Get $get) {
                                                $state = $get('product');
                                                if (!isset($state)) return [];
                                                return ProductUnit::with('unit:id,name')
                                                    ->where('product_id', $state)
                                                    ->get()
                                                    ->pluck('unit.name', 'id');
                                            })
                                            ->native(false)
                                            ->live()
                                            ->label('حدد الوحدة')
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                $unit = ProductUnit::query()->firstWhere('id', $state);
                                                $set('unit_price', $unit->purchasePrice ?? 0);
                                                $set('total', ($unit->purchasePrice ?? 0) * 1);
                                            }),

                                        Quantity::make('quantity')
                                            ->numeric()
                                            ->maxValue(10)
                                            ->minValue(1)
                                            ->live()
                                            ->label('الكمية')
                                            ->required()
                                            ->minValue(1)
                                            ->lazy()
                                            ->afterStateUpdated(function (Forms\Get $get, Set $set, $state) {
                                                $price = floatval($get('unit_price'));
                                                $set('total', ($state * $price));
                                            }),

                                        MoneyInput::make('unit_price')
                                            ->live()
                                            ->lazy()
                                            ->label('سعر الشراء')
                                            ->numeric()
                                            ->required()
                                            ->afterStateUpdated(function (Forms\Get $get, Set $set, $state) {
                                                $quantity = intval($get('quantity'));
                                                $itemTotal = ($state * $quantity);
                                                $set('total', $itemTotal);
                                                $invoiceTotal = floatval($get('total_of_invoice'));
                                                $result = $invoiceTotal + $itemTotal;
                                                $set('total_of_invoice', $result);
                                            }),
                                    ])->columns(4)->live(),


                                Group::make()
                                    ->schema([

                                        DatePicker::make('expire_date')
                                            ->label('تاريخ الإنتهاء')
                                            ->minDate(now()->addDays(2)),

                                        TextInput::make('total')
                                            ->readOnly()
                                            ->label('الإجمالي')
                                            ->live()
                                    ])->live()
                                    ->columns(2),
                                Forms\Components\Toggle::make('enable')->required(false)->label('مفعل'),
                            ])->live()
                    ])
                    ->reorderableWithButtons()
                    ->collapsible()
                    ->live()
                    ->cloneable()
                    ->afterStateUpdated(function (Set $set, $state) {
                        $price = 0;
                        foreach ($state as $item) {
                            $price += floatval($item['data']['total'] ?? '0');
                        }
                        $set('total_of_invoice', $price);
                    })
            ])->live();
    }

    private static function thirdStep(): Forms\Components\Wizard\Step
    {
        return Forms\Components\Wizard\Step::make('الدفع')
            ->schema([
                Forms\Components\Section::make()->schema([

                    Forms\Components\TextInput::make('total_of_invoice')
                        ->statePath('total_of_invoice')
                        ->label('إجمالي الفاتورة')
                        ->default(0)
                        ->readOnly()
                        ->live()
                        ->key('total_of_invoice'),

                    Forms\Components\TextInput::make('price_of_invoice')
                        ->label('المبلغ المدفوع')
                        ->numeric()
                        ->required()
                ])
                    ->columns(2)
            ]);
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


}
