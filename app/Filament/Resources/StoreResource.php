<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreResource\Pages;
use App\Filament\Resources\StoreResource\RelationManagers;
use App\Models\Store;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = "إدارة المخزون";

    protected static ?string $modelLabel = 'مخزن';

    protected static ?string $pluralLabel = 'المخازن';

    protected static ?string $navigationLabel = 'إدارة المخازن';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->label('إسم المخزن')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('الموقع')
                    ->searchable(),
                Tables\Columns\TextColumn::make('manager_name')
                    ->label('إسم المسؤول')
                    ->searchable(),
                Tables\Columns\TextColumn::make('manager_phone')
                    ->label('رقم هاتفه')
                    ->searchable(),
                Tables\Columns\TextColumn::make('manager_email')
                    ->label('بريده الإلكترني')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاريخ آخر تعديل')
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
            'index' => Pages\ListStores::route('/'),
            'create' => Pages\CreateStore::route('/create'),
            'view' => Pages\ViewStore::route('/{record}'),
            'edit' => Pages\EditStore::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

}
