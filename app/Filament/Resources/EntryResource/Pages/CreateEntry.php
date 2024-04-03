<?php

namespace App\Filament\Resources\EntryResource\Pages;

use App\Filament\Resources\EntryResource;
use App\Traits\BackToIndexView;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEntry extends CreateRecord
{
    use BackToIndexView;

    protected static string $resource = EntryResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $creditor = doubleval($data['creditor']) ?? 0;
        $debtor = doubleval($data['debtor']) ?? 0;
        $balance = abs($creditor - $debtor);

        if($debtor <= 0)
        {
            $balance *= -1;
        }

        $data['balance'] = $balance;
        return parent::handleRecordCreation($data);
    }

}
