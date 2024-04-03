<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DelegateResource\Pages;
use App\Filament\Resources\DelegateResource\RelationManagers;
use App\Models\Currency;
use App\Models\Delegate;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Rawilk\FilamentPasswordInput\Password;

class DelegateResource extends Resource
{
    protected static ?string $model = Delegate::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = "الحسابات";

    protected static ?string $modelLabel = 'مندوب';

    protected static ?string $pluralLabel = 'المندوبون';

    protected static ?string $navigationLabel = 'إدارة المندوبون';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->maxLength(255)
                        ->label('اسم المندوب')
                        ->prefixIcon('heroicon-o-user')
                        ->unique(Delegate::class)
                        ->required(),
                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->label('رقم الهاتف')
                        ->prefixIcon('heroicon-o-phone')
                        ->maxLength(30),
                    Forms\Components\TextInput::make('limit')
                        ->numeric()
                        ->prefixIcon('heroicon-o-bell-alert')
                        ->label('سقف الحساب')
                        ->hint('أقصى مديونية مسموحة لهذا الحساب.'),
                    Forms\Components\Textarea::make('address')
                        ->rows(4)
                        ->label('العنوان')
                        ->hintIcon('heroicon-o-map-pin')
                        ->maxLength(255)
                        ->columnSpanFull(),
                ])->columns(3),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->prefixIcon('heroicon-o-at-symbol')
                            ->label('البريد الإلكتروني')
                            ->maxLength(50),
                        Password::make('password')
                            ->label('كلمة المرور')
                            ->inlineSuffix()
                            ->copyable()
                            ->regeneratePassword()
                            ->copyIconColor('warning')
                            ->regeneratePasswordIconColor('primary'),
                    ]),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->label('حساب نشط')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('user.name')->label('المستخدم')->collapsible(),
                Group::make('created_at')->label('تاريخ الإضافة')->date(),
                Group::make('updated_at')->label('تاريخ التعديل')->date(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable()
                    ->label('المستخدم'),
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم المندوب')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->label('العنوان')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('limit')
                    ->label('سقف الحساب')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('حساب نشط'),
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('delegate_report')
                        ->label('كشف حساب')
                        ->icon('heroicon-o-document-arrow-down')
                        ->form([
                            Forms\Components\Group::make()
                                ->schema([
                                    Forms\Components\Select::make('currency')
                                        ->label('العملة')
                                        ->searchable()
                                        ->native(false)
                                        ->preload()
                                        ->options(Currency::all()->pluck('name', 'id'))
                                        ->default(1)
                                        ->required(),
                                    Forms\Components\Section::make()
                                        ->schema([
                                            Forms\Components\DatePicker::make('startDate')->label('من')
                                                ->native(false)
                                                ->placeholder('dd/MM/yyyy')
                                                ->displayFormat('d/m/Y')
                                                ->maxDate(now()->addDay()),
                                            Forms\Components\DatePicker::make('endDate')->label('إلى')
                                                ->native(false)
                                                ->placeholder('dd/MM/yyyy')
                                                ->displayFormat('d/m/Y')
                                                ->maxDate(now()->addDay())
                                        ])->columns(2)
                                ])->columns(1)
                        ])
                        ->action(function (Delegate $record, array $data) {
                            $startDate = $data['startDate'] ?? "null";
                            $endDate = $data['endDate'] ?? "null";
                            $currency = $data['currency'] ?? "null";
                            redirect()->route('delegate.report', [
                                'id' => $record->id,
                                'currency' => $currency,
                                'startDate' => $startDate,
                                'endDate' => $endDate,
                            ]);
                        }),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
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
            RelationManagers\CustomersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDelegates::route('/'),
            'create' => Pages\CreateDelegate::route('/create'),
            //'view' => Pages\ViewDelegate::route('/{record}'),
            'edit' => Pages\EditDelegate::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
