<?php

namespace App\Filament\Pages;

use App\Models\Company;
use Filament\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\FileUpload;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class EditCompany extends Page implements HasForms
{

    use InteractsWithForms;

    public ?array $data = [];

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = "إعدادات النظام";

    protected static ?string $navigationLabel = 'عن النشاط التجاري';

    protected static ?string $label = 'عن النشاط التجاري';

    protected static ?string $title = 'عن النشاط التجاري';


    protected static string $view = 'filament.pages.edit-company';


    public function mount(): void
    {
        $companyInfo = Company::all()->first()?->toArray();
        $this->form->fill($companyInfo);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('المعلومات الأساسية')
                        ->schema([
                            TextInput::make('name')
                                ->label('الإسم')
                                ->required(),
                            Textarea::make('address')
                                ->rows(5)
                                ->autosize()
                                ->label('العنوان')
                        ]),
                    Section::make('أرقام الهاتف')
                        ->schema([
                            TextInput::make('phone1')
                                ->label('رقم الهاتف 1')
                                ->prefixIcon('heroicon-o-phone')
                                ->tel(),
                            TextInput::make('phone2')
                                ->label('رقم الهاتف 2')
                                ->prefixIcon('heroicon-o-phone')
                                ->tel(),
                            TextInput::make('phone3')
                                ->label('رقم الهاتف 3')
                                ->prefixIcon('heroicon-o-phone')
                                ->tel()
                        ])->columns([
                            'sm' => 1,
                            'md' => 1,
                            'lg' => 1,
                            'xl' => 3,
                            '2xl' => 3,
                        ]),
                ])->columnSpan([
                    'sm' => 'full',
                    'md' => 'full',
                    'lg' => 'full',
                    'xl' => 2,
                    '2xl' => 2,
                ]),

                Group::make()->schema([
                    Section::make('أخري')
                        ->schema([
                            TextInput::make('email')
                                ->label('البريد الإلكتروني')
                                ->prefixIcon('heroicon-o-at-symbol')
                                ->email(),
                            TextInput::make('website')
                                ->label('الموقع الإلكتروني')
                                ->prefixIcon('heroicon-o-globe-alt')
                                ->url(),
                            FileUpload::make('image')
                                ->label('الشعار')
                                ->image()
                                ->directory('images')
                        ])
                ])

            ])->columns([
                'sm' => 1,
                'md' => 1,
                'lg' => 1,
                'xl' => 3,
                '2xl' => 3,
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        try {
            $formData = $this->form->getState();
            $companyId = Company::all()->first()?->id;
            $company = Company::find($companyId);

            if (!$company) {
                Company::create($formData);
            } else {
                // Update the existing record
                $company->update($formData);
            }
        } catch (Halt $exception) {
            return;
        }

        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->body('تم تحديث بيانات النشاط التجاري بنجاح.')
            ->color('success')
            ->send();
    }
}
