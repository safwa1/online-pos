<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Delegate;
use Carbon\Carbon;
use JetBrains\PhpStorm\NoReturn;

class DelegateController extends Controller
{
    #[NoReturn]
    public function index($id, $currencyId, $startDate, $endDate)
    {
        $date = "";
        $delegate = Delegate::query()->find($id);
        $entries = $delegate->entries->where('currency_id', $currencyId);

        if ($startDate !== null && $endDate !== null && $startDate !== 'null' && $endDate !== 'null') {
            $date = str_replace("-", "/", $startDate) . ' - ' . str_replace("-", "/", $endDate);
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
            $entries = $entries->whereBetween('date', [$startDate, $endDate]);
        }

        $entries = $entries->toArray();
        $currency = !empty($entries) ? $entries[0]['currency'] : Currency::query()->find($currencyId)->toArray();

        $totalOfCreditor = 0.0;
        $totalOfDebtor = 0.0;
        $totalOfBalance = 0.0;

        foreach ($entries as $entry) {
            $totalOfCreditor += floatval($entry['creditor'] ?? '0');
            $totalOfDebtor += floatval($entry['debtor'] ?? '0');
            $totalOfBalance += floatval($entry['balance'] ?? '0');
        }

        return view('components.customer-report-table', [
            'account' => $delegate->toArray(),
            'entries' => $entries,
            'total' => [
                'creditor' => $totalOfCreditor,
                'debtor' => $totalOfDebtor,
                'balance' => $totalOfBalance
            ],
            'date' => $date,
            'currency' => $currency
        ]);
    }
}
