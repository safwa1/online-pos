<?php

namespace App\Filament\Resources\ExpensesResource\Pages;

use App\Models\Expenses;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ExpensesResource;


class ListExpenses extends ListRecords
{
    protected static string $resource = ExpensesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all_records' => Tab::make('all')
                ->label('الكل')
                ->icon('heroicon-o-bars-3-bottom-left')
                ->badge(Expenses::where('deleted_at', null)->get()->count()),
            'trashed_ony' => Tab::make('trashed_ony')
                ->label('سلة المحذوفات')
                ->icon('heroicon-o-trash')
                ->badge(Expenses::onlyTrashed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                })
        ];
    }
}
