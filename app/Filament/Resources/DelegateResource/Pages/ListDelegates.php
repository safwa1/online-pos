<?php

namespace App\Filament\Resources\DelegateResource\Pages;

use App\Filament\Resources\DelegateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDelegates extends ListRecords
{
    protected static string $resource = DelegateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
