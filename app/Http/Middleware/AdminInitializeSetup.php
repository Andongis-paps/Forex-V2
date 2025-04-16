<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\BufferStocksManagement;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

class AdminInitializeSetup {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next) {
        $date_now = Carbon::parse(env('DATENOW', now()->format('Y-m-d')))->format('Y-m-d');
        $ID = 0;
        $warnings = [];
        $main_routes = [];
        $current_route = Route::currentRouteName();
        $pending_serial = BufferStocksManagement::serials();
        $break_down = BufferStocksManagement::breakDown();

        if (count($break_down) <> 0) {
            $ID = $break_down[0]->BFID;
        } else if (count($pending_serial) <> 0) {
            $ID = $pending_serial[0]->BFID;
        }

        function prepareWarning($transaction, $type) {
            if (count($transaction) > 0) {
                $count = count($transaction);

                if ($type === "BreakDown") {
                    $no_break_d_deets = '';

                    foreach ($transaction as $list) {
                        $no_break_d_deets .=
                            '<tr>
                                <td class="text-sm text-black p-1 text-center">'. $list->BFNo .'</td>
                                <td class="text-sm text-black p-1 text-center">'. $list->Currency .'</td>
                                <td class="text-sm text-black py-1 pe-3 text-right"><strong>'. number_format($list->DollarAmount, 2) .'</strong></td>
                                <td class="text-sm text-black py-1 pe-3 text-right"><strong>'. number_format($list->Principal, 2) .'</strong></td>
                            </tr>';
                    }
                
                    $message =
                    "<small>Please be reminded to process the breakdown of buffer financing entries listed below.  
                        <table class=\"table table-bordered table-hover mt-2 mb-0\" style=\"overflow-y: scroll !important;\">  
                            <thead>
                                <tr>
                                    <th class=\"text-xs text-black font-bold text-center p-1\">Buffer No.</th>
                                    <th class=\"text-xs text-black font-bold text-center p-1\">Currency</th>
                                    <th class=\"text-xs text-black font-bold text-center p-1\">Amount</th>
                                    <th class=\"text-xs text-black font-bold text-center p-1\">Principal</th>
                                </tr>
                            </thead>    
                            <tbody>
                                <tr>
                                    ". $no_break_d_deets ."
                                </tr>
                            </tbody>
                        </table>    
                    </small>
                    <div class=\"col-12\">
                    </div>";
                } else if ($type === "PendingSerials") {
                    $pending_serial_deets = '';

                    foreach ($transaction as $list) {
                        $pending_serial_deets .=
                            '<tr>
                                <td class="text-sm text-black p-1 text-center">'. number_format($list->BillAmount, 2) .'</td>
                                <td class="text-sm text-black p-1 text-center">'. $list->quantity .'</td>
                                <td class="text-sm text-black py-1 pe-3 text-right"><strong>'. number_format($list->total_amount, 2) .'</strong></td>
                            </tr>';
                    }
                
                    $message =
                    "<small>Please be reminded to process the pending serials (buffer) for the following denominations.  
                        <table class=\"table table-bordered table-hover mt-2 mb-0\" style=\"overflow-y: scroll !important;\">  
                            <thead>
                                <tr>
                                    <th class=\"text-xs text-black font-bold text-center p-1\">Denomination</th>
                                    <th class=\"text-xs text-black font-bold text-center p-1\">Quantity</th>
                                    <th class=\"text-xs text-black font-bold text-center p-1\">Total Amount</th>
                                </tr>
                            </thead>    
                            <tbody>
                                <tr>
                                    ". $pending_serial_deets ."
                                </tr>
                            </tbody>
                        </table>    
                    </small>
                    <div class=\"col-12\">
                    </div>";
                }
                return $message;
            }
            return null;
        }

        // Collect warnings
        if ($warning = prepareWarning($break_down, "BreakDown")) {
            $warnings['pending_serials'] = ['route' => [
                'admin_transactions.buffer.buffer_serials',
                'admin_transactions.buffer.break_d_finance'
            ],
            'message' => $warning];
        }

        // Collect warnings
        if ($warning = prepareWarning($pending_serial, "PendingSerials")) {
            $warnings['pending_serials'] = ['route' => [
                    'admin_transactions.buffer.buffer_serials',
                    'admin_transactions.buffer.break_d_finance'
                ],
            'message' => $warning];
        }

        foreach ($warnings as $warning) {
            $routes =  $warning['route'] ?? [$warning['route']];

            foreach ($routes as $route) {
                $main_routes[] = $route;
            }
        }

        $is_main_route = false;
        
        foreach ($main_routes as $routes) {
            if (strpos($current_route, $routes) === 0) {
                $is_main_route = true;
                break;
            }
        }

        if ($is_main_route) {
            session()->put('show_popup', false);
            return $next($request);
        }

        foreach ($warnings as $key => $warning) {
            $routes =  $warning['route'] ?? [$warning['route']];

            foreach ($routes as $route) {
                if ($request->ajax() || $request->wantsJson()) {
                    return $next($request);
                }

                if (strpos($current_route, $route) === false) {
                    return redirect()->route($route, [$ID])->with(['warning' => 1, 'title' => 'Action Needed', 'html' => $warning['message']]);
                }
            }
        }

        session()->put('show_popup', true);

        return $next($request);
    }
}
