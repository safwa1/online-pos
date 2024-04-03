<?php

namespace App\Filament\Resources\DelegateResource\RelationManagers;

use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;


class CustomersRelationManager extends RelationManager
{
    protected static string $relationship = 'customers';

    protected static ?string $title = 'العملاء';

    protected static ?string $label = "عميل";

    protected static ?string $modelLabel = "عميل";

    protected static ?string $pluralLabel = "عميل";
    protected static ?string $pluralModelLabel = "العملاء";
    protected static bool $isLazy = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->prefixIcon('heroicon-o-user')
                        ->label('اسم العميل')
                        ->maxLength(255)
                        ->unique(Customer::class)
                        ->required(),
                    Forms\Components\TextInput::make('phone')
                        ->label('رقم الهاتف')
                        ->prefixIcon('heroicon-o-phone')
                        ->tel()
                        ->maxLength(30),
                    Forms\Components\TextInput::make('email')
                        ->prefixIcon('heroicon-o-at-symbol')
                        ->label('البريد الإلكتروني')
                        ->email()
                        ->maxLength(50),
                    Forms\Components\TextInput::make('limit')
                        ->prefixIcon('heroicon-o-bell-alert')
                        ->label('سقف الحساب')
                        ->helperText('أقصى مديونية مسموحة لهذا الحساب.')
                        ->numeric(),
                    Forms\Components\Textarea::make('address')
                        ->maxLength(255)
                        ->rows(4)
                        ->label('العنوان')
                        ->hintIcon('heroicon-o-map-pin')
                        ->columnSpanFull(),
                ])->columns(2),
                Forms\Components\Toggle::make('is_active')
                    ->label('حساب نشط')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم العميل')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('العنوان')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('limit')
                    ->label('سقف الحساب')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('حساب نشط')
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
