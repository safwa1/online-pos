<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpensesResource\Pages;
use App\Filament\Resources\ExpensesResource\RelationManagers;
use App\Models\Expenses;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;


class ExpensesResource extends Resource
{
    protected static ?string $model = Expenses::class;


    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-down';

    protected static ?string $navigationGroup = "الحسابات";

    protected static ?string $modelLabel = 'مصروف';

    protected static ?string $pluralLabel = 'المصروفات';

    protected static ?string $navigationLabel = 'إدارة المصروفات';

    protected static ?int $navigationSort = 4;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('type_id')
                            ->relationship('type', 'name')
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('إسم المصروف')
                                    ->required()
                            ])
                            ->label('نوع المصروف')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->rows(4)
                            ->maxLength(255)
                            ->label('البيان')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->label('المبلغ'),
                        Forms\Components\DateTimePicker::make('date')
                            ->native(false)
                            ->placeholder('dd/MM/yyyy')
                            ->displayFormat('d/m/Y')
                            ->label('التاريخ')
                            ->maxDate(now()->addDay()),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('user.name')->label('المستخدم')->collapsible(),
                Group::make('type.name')->label('نوع المصروف')->collapsible(),
                Group::make('date')->label('التاريخ')->collapsible(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable()
                    ->label('المستخدم'),
                Tables\Columns\TextColumn::make('type.name')
                    ->label('نوع المصروف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->wrap()
                    ->limit('50')
                    ->tooltip(fn($record): string => $record->description)
                    ->label('البيان')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('المبلغ')
                    ->sortable()
                    ->summarize([
                        Sum::make()->numeric(
                            decimalPlaces: 2,
                            decimalSeparator: '.',
                            thousandsSeparator: ',',
                        ),
                    ]),
                Tables\Columns\TextColumn::make('date')
                    ->label('التاريخ')
                    ->date('d/m/Y')
                    ->sortable(),
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
                Tables\Actions\ViewAction::make()
                ->hidden(fn($record) => $record->trashed()),
                Tables\Actions\EditAction::make()
                    ->hidden(fn($record) => $record->trashed()),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpenses::route('/create'),
            'view' => Pages\ViewExpenses::route('/{record}'),
            'edit' => Pages\EditExpenses::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
