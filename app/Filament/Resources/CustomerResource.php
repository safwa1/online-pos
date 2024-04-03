<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Currency;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\GlobalSearch\Actions\Action;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = "الحسابات";

    protected static ?string $modelLabel = 'عميل';

    protected static ?string $pluralLabel = 'العملاء';

    protected static ?string $navigationLabel = 'إدارة العملاء';

    protected static ?string $recordTitleAttribute = "name";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->collapsible()
                    ->schema([
                        Forms\Components\Select::make('group_id')
                            ->relationship('group', 'name')
                            ->live()
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->prefixIcon('heroicon-o-tag')
                            ->label('المجموعة')
                            ->createOptionForm(self::createGroupFrom())
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->prefixIcon('heroicon-o-user')
                            ->label('اسم العميل')
                            ->maxLength(255)
                            ->required()->unique(Customer::class),
                        Forms\Components\Select::make('delegate_id')
                            ->relationship('delegate', 'name')
                            ->live()
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->prefixIcon('heroicon-o-user')
                            ->label('المندوب')
                            ->createOptionForm(self::createDelegateFrom()),
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
                            ->numeric()
                            ->columnSpanFull(),
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

    public static function table(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('user.name')->label('المستخدم')->collapsible(),
                Group::make('delegate.name')->label('المندوب')->collapsible(),
                Group::make('group.name')->label('المجموعة')->collapsible(),
                Group::make('created_at')->label('تاريخ الإضافة')->date(),
                Group::make('updated_at')->label('تاريخ التعديل')->date(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('group.name')
                    ->numeric()
                    ->sortable()
                    ->label('المجموعة'),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable()
                    ->label('المستخدم')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('delegate.name')
                    ->numeric()
                    ->sortable()
                    ->label('المندوب')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم العميل')
                    ->limit(50)
                    ->wrap()
                    ->tooltip(fn($record): string => $record->name)
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
                    ->limit(50)
                    ->wrap()
                    ->tooltip(fn($record): string => $record?->address ?? '')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('limit')
                    ->label('سقف الحساب')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('حساب نشط'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('تاريخ التحديث')
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
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('show_report')
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
                        ->action(function (Customer $record, array $data) {
                            $startDate = $data['startDate'] ?? "null";
                            $endDate = $data['endDate'] ?? "null";
                            $currency = $data['currency'] ?? "null";
                            redirect()->route('customer.report', [
                                'id' => $record->id,
                                'currency' => $currency,
                                'startDate' => $startDate,
                                'endDate' => $endDate,
                            ]);
                        })
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('download_report')
                        ->label('تحميل كشف حساب')
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
                        ->action(function (Customer $record, array $data) {
                            $startDate = $data['startDate'] ?? "null";
                            $endDate = $data['endDate'] ?? "null";
                            $currency = $data['currency'] ?? "null";

                            redirect()->route('download-as-pdf', [
                                'id' => $record->id,
                                'currency' => $currency,
                                'startDate' => $startDate,
                                'endDate' => $endDate,
                            ]);
                        })
                        ->openUrlInNewTab(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                ]),
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
            RelationManagers\EntriesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    private static function createGroupFrom(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->maxLength(255)
                ->label('اسم المجموعة')
        ];
    }

    private static function createDelegateFrom(): array
    {
        return [
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')
                    ->maxLength(255)
                    ->label('اسم المندوب')
                    ->prefixIcon('heroicon-o-user'),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->label('رقم الهاتف')
                    ->prefixIcon('heroicon-o-phone')
                    ->maxLength(30),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->prefixIcon('heroicon-o-at-symbol')
                    ->label('البريد الإلكتروني')
                    ->maxLength(50),
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
            ])->columns(2),
            Forms\Components\Toggle::make('is_active')
                ->required()
                ->label('حساب نشط')
                ->default(true),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'phone', 'email'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'name' => $record->name,
            'address' => $record->address,
        ];
    }

    public static function getGlobalSearchResultActions(Model $record): array
    {
        return [
            Action::make('view')
                ->label('عرض')
                ->url(static::getUrl('view', ['record' => $record]), shouldOpenInNewTab: true),
            Action::make('edit')
                ->label('تعديل')
                ->url(static::getUrl('edit', ['record' => $record])),
        ];
    }
}
