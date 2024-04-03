<?php

namespace App\Filament\Resources\EntryResource\Pages;

use App\Filament\Resources\EntryResource;
use App\Models\Entry;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListEntries extends ListRecords
{
    protected static string $resource = EntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('all')
                ->label('الكل')
                ->icon('heroicon-o-bars-3-bottom-left')
                ->badge(Entry::all()->count()),

            'customers_entries' => Tab::make('customers_entries')
                ->label('العملاء')
                ->icon('heroicon-o-users')
                ->badge(Entry::where('account_type', 'customer')->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->where('account_type', 'customer');
                }),

            'suppliers_entries' => Tab::make('suppliers_entries')
                ->label('الموردين')
                ->icon('heroicon-o-user-group')
                ->badge(Entry::where('account_type', 'supplier')->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->where('account_type', 'supplier');
                }),

            'delegates_entries' => Tab::make('delegates_entries')
                ->label('المندوبين')
                ->icon('heroicon-o-user-circle')
                ->badge(Entry::where('account_type', 'delegate')->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->where('account_type', 'delegate');
                }),

            'trashed' => Tab::make('trashed_ony')
                ->label('سلة المحذوفات')
                ->icon('heroicon-o-trash')
                ->badge(Entry::onlyTrashed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                })
        ];
    }

}
