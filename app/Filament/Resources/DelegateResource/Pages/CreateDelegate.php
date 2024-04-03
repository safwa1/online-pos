<?php

namespace App\Filament\Resources\DelegateResource\Pages;

use App\Filament\Resources\DelegateResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateDelegate extends CreateRecord
{
    protected static string $resource = DelegateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        unset($data['password']);

        return parent::handleRecordCreation($data); // TODO: Change the autogenerated stub
    }
}
