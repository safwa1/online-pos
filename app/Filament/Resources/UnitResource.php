<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitResource\Pages;
use App\Filament\Resources\UnitResource\RelationManagers;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = "إعدادات النظام";

    protected static ?string $modelLabel = 'وحدة';

    protected static ?string $pluralLabel = 'الوحدات';

    protected static ?string $navigationLabel = 'إدارة الوحدات';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('إسم الوحدة'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('symbol')
                    ->label(__('رمز الوحدة'))
                    ->maxLength(8),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('إسم الوحدة'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('symbol')
                    ->label(__('رمز الوحدة'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('تاريخ الإنشاء'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('تاريخ آخر تعديل'))
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
            'index' => Pages\ListUnits::route('/'),
            //'create' => Pages\CreateUnit::route('/create'),
            //'view' => Pages\ViewUnit::route('/{record}'),
            //'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }
}
