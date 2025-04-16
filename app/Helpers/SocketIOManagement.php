<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SocketIOManagement {
    public static function updates(Request $request) {
        $query = DB::connection('forex')->table('tblcurrencydenom as td')
            ->where('td.CurrencyID', $request->get('currency_id'))
            ->where('td.BranchID', $request->get('userBranchID'))
            ->whereNotIn('td.TransType', [3, 4])
            ->where('td.StopBuying', 0);

        $buying_rates = $query->clone()
            ->selectRaw('td.BillAmount, td.SinagRateBuying')
            ->groupBy('td.BillAmount', 'td.SinagRateBuying')
            ->get();

        $response = [
            'buying_rates' => $buying_rates
        ];

        dd($response);

        return response()->json($response);
    }
}
