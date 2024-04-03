<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Customer;
use Carbon\Carbon;
use JetBrains\PhpStorm\NoReturn;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf;

class CustomerController extends Controller
{
    #[NoReturn]
    public function index($id, $currencyId, $startDate, $endDate)
    {
        $date = "";
        $customer = Customer::query()->find($id);
        $entries = $customer->entries->where('currency_id', $currencyId);

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
            'account' => $customer->toArray(),
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

    public function downloadAsPdf($id, $currencyId, $startDate, $endDate)
    {
        $date = "";
        $customer = Customer::query()->find($id);
        $entries = $customer->entries->where('currency_id', $currencyId);

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

        $data = [
            'account' => $customer->toArray(),
            'entries' => $entries,
            'total' => [
                'creditor' => $totalOfCreditor,
                'debtor' => $totalOfDebtor,
                'balance' => $totalOfBalance
            ],
            'date' => $date,
            'currency' => $currency
        ];

        $pdf = LaravelMpdf::loadView('components.customer-report-table', $data);
        //return $pdf->stream( $customer->name . '.pdf');
    }
}
