<?php

namespace App\Http\Controllers\Web;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GlobalController extends Controller {
    public function rateUpdates(Request $request) {
        $query = DB::connection('forex')->table('tblcurrencydenom as td')
            ->join('tblcurrency as tc', 'td.CurrencyID', 'tc.CurrencyID')
            ->where('td.CurrencyID', $request->get('currency_id'))
            ->where('td.BranchID', $request->get('userBranchID'))
            ->whereNotIn('td.TransType', [3, 4])
            ->where('td.StopBuying', 0);

        $buying_rates = $query->clone()
            ->selectRaw('tc.Currency, td.BillAmount,
                SUM(? - td.VarianceBuying) as previous_rate,
                SUM(? - td.VarianceBuying) as updated_rate',
                [$request->get('old_rate'), $request->get('new_rate')]
            )
            ->groupBy('tc.Currency', 'td.BillAmount')
            ->get();

        $selling_rates = $query->clone()
            ->selectRaw('tc.Currency, td.BillAmount,
                SUM(? + td.VarianceSelling) as previous_rate,
                SUM(? + td.VarianceSelling) as updated_rate',
                [$request->get('old_rate'), $request->get('new_rate')]
            )
            ->groupBy('tc.Currency', 'td.BillAmount')
            ->get();

        $response = [
            'buying_rates' => $buying_rates,
            'selling_rates' => $selling_rates,
        ];

        return response()->json($response);
    }
}
