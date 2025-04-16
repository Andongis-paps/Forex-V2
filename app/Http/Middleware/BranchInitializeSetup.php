<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\TransafersManagement;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

class BranchInitializeSetup {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next){
        $date_now = Carbon::parse(env('DATENOW', now()->format('Y-m-d')))->format('Y-m-d');
        $warnings = [];
        $main_routes = [];
        $current_route = Route::currentRouteName();
        $pending_buffers = TransafersManagement::buffers();

        function prepareWarning($transaction, $type) {
            if (count($transaction) > 0) {
                $count = count($transaction);

                if ($type === "PendingBuffer") {
                    $branches = '';
                    $transf_type = '';
                    $transfer_deets = '';

                    foreach ($transaction as $list) {
                        $branches .=
                            '<tr>
                                <td class="text-sm text-black p-1 text-center">'. $list->BranchCode .'</td>
                            </tr>';

                        $transfer_deets .=
                            '<tr>
                                <td class="text-sm text-black p-1 text-center">'. $list->Remarks .'</td>
                                <td class="text-sm text-black p-1 text-center"><strong>'. $list->TFXNo .'</strong></td>
                            </tr>';
                    }
                
                    $message =
                    "<small>Please be reminded to process the unacknowledged buffers listed below.  
                        <table class=\"table table-bordered table-hover mt-2 mb-0\" style=\"overflow-y: scroll !important;\">  
                            <thead>
                                <tr>
                                    <th class=\"text-xs text-black font-bold text-center p-1\">Transfer Type</th>
                                    <th class=\"text-xs text-black font-bold text-center p-1\">Trnasfer FX No.</th>
                                </tr>
                            </thead>    
                            <tbody>
                                <tr>
                                    ". $transfer_deets ."
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
        if ($warning = prepareWarning($pending_buffers, "PendingBuffer")) {
            $warnings['pending_buffer'] = ['route' => 'branch_transactions.transfer_forex', 'message' => $warning];
        }

        foreach ($warnings as $warning) {
            $main_routes[] = $warning['route'];
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
            if (strpos($current_route, $warning['route']) === false) {
                return redirect()->route($warning['route'])->with(['warning' => 1, 'title' => 'Action Needed', 'html' => $warning['message']]);
            }
        }

        session()->put('show_popup', true);

        return $next($request);
    }
}
