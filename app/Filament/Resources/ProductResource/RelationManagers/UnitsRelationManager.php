<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;


use App\Models\ProductUnit;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class UnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'units';
    protected static ?string $label = "وحدة";
    protected static ?string $title = "وحدات المنتج";

    protected static bool $isLazy = false;


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Select::make('unit_id')
                        ->relationship('unit', 'name')
                        ->label('إسم الوحدة')
                        ->native(false)
                        ->preload()
                        ->searchable()
                        ->live()
                        ->required(),
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

                        }))
                ])->columns(3)->columnSpanFull(),
                Forms\Components\Group::make()->schema([
                    Forms\Components\TextInput::make('purchasePrice')
                        ->required()
                        ->numeric()
                        ->label('سعر الشراء'),
                    Forms\Components\TextInput::make('salePrice')
                        ->required()
                        ->numeric()
                        ->label('سعر البيع'),
                    Forms\Components\TextInput::make('minSalePrice')
                        ->required()
                        ->numeric()
                        ->label('أقل سعر بيع')
                ])->columns(3)->columnSpanFull(),
                Forms\Components\Toggle::make('isMainUnit')
                    ->required()
                    ->live()
                    ->label('وحدة إفتراضية')
                    ->hidden(function (Forms\Get $get, $operation) {
                        $isCreate = $operation == 'create';
                        $isEdit = $operation == 'edit';
                        $hasDefaultUnit = ProductUnit::where('isMainUnit', true)->exists();
                        if ($isEdit) {
                            $id = $this->cachedMountedTableActionRecord->id;
                            $isDefault = ProductUnit::find($id)->isMainUnit == 1;
                            if ($isDefault) return false;
                            else return $hasDefaultUnit;
                        }

                        return $isCreate && $hasDefaultUnit;
                    })
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('unit.name')
            ->columns([
                //Tables\Columns\TextColumn::make('unit.name'),
                Tables\Columns\TextColumn::make('unit.name')
                    ->numeric()
                    ->sortable()
                    ->label('إسم الوحدة')
                    ->searchable(),
                Tables\Columns\TextColumn::make('count')
                    ->numeric()
                    ->sortable()
                    ->label('السعة'),
                Tables\Columns\TextColumn::make('barCode')
                    ->searchable()
                    ->label('الباركود')
                    ->searchable(),
                Tables\Columns\TextColumn::make('purchasePrice')
                    ->label('سعر الشراء')
                    ->sortable(),
                Tables\Columns\TextColumn::make('salePrice')
                    ->label('سعر البيع')
                    ->sortable(),
                Tables\Columns\TextColumn::make('minSalePrice')
                    ->label('أقل سعر بيع')
                    ->sortable(),
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
                    ->label('تاريخ التعديل')
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
