<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Validator;
use App;
use Lang;
use App\Admin;
use App\Models\User;
use DB;
use Illuminate\Support\Carbon;
use Hash;
use Auth;
use Session;
use Illuminate\Http\Request;

class AdminSellingTransactController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:BULK SELLING,VIEW')->only(['show', 'details', 'serials']);
        $this->middleware('check.access.permission:BULK SELLING,ADD')->only(['add', 'save', 'queue', 'sell', 'queued']);
        $this->middleware('check.access.permission:BULK SELLING,EDIT')->only(['edit', 'update', 'print', 'unselect', 'unqueueCappedBills', 'cappedBills']);
        $this->middleware('check.access.permission:BULK SELLING,DELETE')->only(['void']);
        $this->middleware('check.access.permission:BULK SELLING,PRINT')->only(['printQueued']);
    }

    protected function available() {
        $branch_stocks_query = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('fs.Queued, fs.QueuedBy, tc.Currency, tc.CurrAbbv, fs.Buffer, fd.CurrencyID, d.SinagRateBuying, fs.CMRUsed, SUM(fs.BillAmount) as bill_amount, COUNT(fs.BillAmount) as bill_count, SUM(fs.BillAmount * fs.CMRUsed) as exchange_amount, SUM(fs.BillAmount * d.SinagRateBuying) as principal, GROUP_CONCAT(fs.FSID) as FSIDs, NULL as AFSIDs, 2 as source')
            ->join('tblforexserials AS fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->join('tbldenom AS d', 'fs.DenomID', '=', 'd.DenomID')
            ->join('tbltransactiontype as tt', 'fd.TransType', 'tt.TTID')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            // ->where('fs.Buffer', 0)
            // ->whereNotNull('fs.QueuedBy')
            // ->where('fs.Queued', 1)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.Transfer', 1)
            ->whereNotNull('fs.FTDID')
            ->where('fs.Received', 1)
            ->where('fs.FSStat', 2)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->groupBy('fs.Queued', 'fs.QueuedBy', 'tc.Currency', 'tc.CurrAbbv', 'fd.CurrencyID', 'd.SinagRateBuying', 'fs.CMRUsed', 'fs.Buffer');

        $admin_stocks_query = DB::connection('forex')->table('tbladminforexserials AS fs')
            ->selectRaw('fs.Queued, fs.QueuedBy, tc.Currency, tc.CurrAbbv, fs.Buffer, COALESCE(fd.CurrencyID, bf.CurrencyID) as CurrencyID, d.SinagRateBuying, fs.CMRUsed, SUM(fs.BillAmount) as bill_amount, COUNT(fs.BillAmount) as bill_count, SUM(fs.BillAmount * fs.CMRUsed) as exchange_amount, SUM(fs.BillAmount * d.SinagRateBuying) as principal, NULL as FSIDs, GROUP_CONCAT(fs.AFSID) as AFSIDs, 1 as source')
            ->leftJoin('tbladminbuyingtransact AS fd', 'fs.aftdid', '=', 'fd.aftdid')
            ->leftJoin('tblbufferfinancing AS bf', 'fs.bfid', '=', 'bf.bfid')
            ->join('tbladmindenom AS d', 'fs.adenomid', '=', 'd.adenomid')
            ->leftJoin('tbltransactiontype as tt', 'fd.TransType', 'tt.TTID')
            ->leftJoin('tblcurrency as tc', function($join) {
                $join->on('fd.CurrencyID', '=', 'tc.CurrencyID')
                    ->orOn('bf.CurrencyID', '=', 'tc.CurrencyID');
            })
            // ->where('fs.Buffer', 0)
            // ->whereNotNull('fs.QueuedBy')
            // ->where('fs.Queued', 1)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.FSStat', 1)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->groupBy('fs.Queued', 'fs.QueuedBy', 'tc.Currency', 'tc.CurrAbbv', 'CurrencyID', 'd.SinagRateBuying', 'fs.CMRUsed', 'fs.Buffer');

        $joined_queries = $branch_stocks_query->unionAll($admin_stocks_query);

        return $joined_queries;
    }

    public function show(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['bills_sold_to_mnl'] = DB::connection('forex')->table('tblsoldtomaniladetails')
            ->join('pawnshop.tblxusers', 'tblsoldtomaniladetails.UserID', 'pawnshop.tblxusers.UserID')
            ->join('pawnshop.tblxcustomer' , 'tblsoldtomaniladetails.CustomerID' , 'pawnshop.tblxcustomer.CustomerID')
            ->orderBy('tblsoldtomaniladetails.STMDID', 'DESC')
            ->paginate(10);

        // dd($result['bills_sold_to_mnl'] );

        return view('selling_transact_admin.add_new_selling_transact_admin', compact('result', 'menu_id'));
    }

    public function queue(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $sub_queries = $this->available();

        $result['queued_bills'] = DB::connection('forex')->query()->fromSub($sub_queries, 'combined')
            ->selectRaw('CurrAbbv, CurrencyID, Buffer, SinagRateBuying, CMRUsed, SUM(bill_amount) as total_bill_amount, SUM(bill_count) as total_bill_count, SUM(exchange_amount) as total_exchange_amount, SUM(principal) as total_principal')
            ->selectRaw('GROUP_CONCAT(FSIDs) as All_FSIDs')
            ->selectRaw('GROUP_CONCAT(AFSIDs) as All_AFSIDs')
            ->selectRaw('SUM(exchange_amount) - SUM(principal) as gain_loss')
            ->groupBy('CurrAbbv', 'CurrencyID', 'SinagRateBuying', 'CMRUsed', 'Buffer')
            ->whereNotNull('QueuedBy')
            ->where('Queued', 1)
            ->get();

        $buffers = DB::connection('forex')->query()->fromSub($sub_queries, 'combined')
            ->selectRaw('COUNT(Buffer) as Buffers')
            ->where('Queued', 1)
            ->where('Buffer', 1)
            ->value('Buffers');

        $result['buffer_if_any'] = $buffers > 0 ? true : false;

        $result['transact_type'] = DB::connection('forex')->table('tbltransactiontype')
            ->where('tbltransactiontype.TransType', '!=', 'DPOFX')
            ->where('tbltransactiontype.Active', '=', 1)
            ->get();
        
        $usd_rate = DB::connection('forex')->table('tblcurrentrate as cr')
            ->selectRaw('MAX(CRID) as CRID')
            ->where('cr.CurrencyID', 11)
            ->value('CRID');

        $result['buffer_rate'] = DB::connection('forex')->table('tblcurrentrate as cr')
            ->where('cr.CRID', $usd_rate)
            ->value('cr.Rate');

        return view('selling_transact_admin.consolidate_bills_admin', compact('result', 'menu_id'));
    }

    public function currencies(Request $request) {
        $sub_queries = $this->available();

        $if_buffer = DB::connection('forex')->query()->fromSub($sub_queries, 'combined')
            ->selectRaw('combined.CurrencyID, combined.Buffer')
            ->groupBy('combined.CurrencyID', 'combined.Buffer')
            ->get();

        $currencies = DB::connection('forex')->table('tblcurrentrate as cr')
            ->selectRaw('tc.CurrencyID, tc.Currency, tc.CurrAbbv, MAX(CRID) as CRID, MAX(cr.EntryDateTime) as MaxEntryDateTime')
            ->join('tblcurrency as tc', 'cr.CurrencyID', '=', 'tc.CurrencyID')
            ->when($request->get('selected_value') == '1', function ($query) use ($if_buffer) {
                $w_out_buffer = $if_buffer->where('Buffer', 0)->pluck('CurrencyID')->toArray();
                return $query->whereIn('tc.CurrencyID', $w_out_buffer);
            })
            ->when($request->get('selected_value') == 'buffer', function ($query) use ($if_buffer) {
                $w_buffer = $if_buffer->where('Buffer', 1)->pluck('CurrencyID')->toArray();
                return $query->whereIn('tc.CurrencyID', $w_buffer);
            })
            ->groupBy('tc.CurrencyID', 'tc.Currency', 'tc.CurrAbbv')
            ->orderBy('tc.Currency', 'ASC')
            ->get();

        $curr_rate = [];

        foreach ($currencies as $index => $details) {
            $get_curr_rate_deets = DB::connection('forex')->table('tblcurrentrate')
                ->selectRaw('tblcurrentrate.Rate')
                ->where('tblcurrentrate.CRID', '=', $details->CRID)
                ->get();

            $rate = [];

            foreach ($get_curr_rate_deets as $get_rate) {
                $rate[] = $get_rate;
            }

            $details->Rate = $rate;

            $curr_rate[] = $details;
        }

        $response = [
            'currencies' => $currencies
        ];

        return response()->json($response);
    }

    public function printQueued(Request $request) {
        $branch_stocks_query = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('fs.Queued, fs.QueuedBy, tc.Currency, tc.CurrAbbv, fs.Buffer, fd.CurrencyID, d.SinagRateBuying, fs.CMRUsed, SUM(fs.BillAmount) as bill_amount, COUNT(fs.BillAmount) as bill_count, SUM(fs.BillAmount * fs.CMRUsed) as exchange_amount, SUM(fs.BillAmount * d.SinagRateBuying) as principal, GROUP_CONCAT(fs.FSID) as FSIDs, NULL as AFSIDs, 2 as source')
            ->join('tblforexserials AS fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->join('tbldenom AS d', 'fs.DenomID', '=', 'd.DenomID')
            ->join('tbltransactiontype as tt', 'fd.TransType', 'tt.TTID')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.Transfer', 1)
            ->whereNotNull('fs.FTDID')
            ->where('fs.Received', 1)
            ->where('fs.FSStat', 2)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->groupBy('fs.Queued', 'fs.QueuedBy', 'tc.Currency', 'tc.CurrAbbv', 'fd.CurrencyID', 'd.SinagRateBuying', 'fs.CMRUsed', 'fs.Buffer');

        $admin_stocks_query = DB::connection('forex')->table('tbladminforexserials AS fs')
            ->selectRaw('fs.Queued, fs.QueuedBy, tc.Currency, tc.CurrAbbv, fs.Buffer, bf.CurrencyID, d.SinagRateBuying, fs.CMRUsed, SUM(fs.BillAmount) as bill_amount, COUNT(fs.BillAmount) as bill_count, SUM(fs.BillAmount * fs.CMRUsed) as exchange_amount, SUM(fs.BillAmount * d.SinagRateBuying) as principal, NULL as FSIDs, GROUP_CONCAT(fs.AFSID) as AFSIDs, 1 as source')
            ->leftJoin('tbladminbuyingtransact AS fd', 'fs.aftdid', '=', 'fd.aftdid')
            ->leftJoin('tblbufferfinancing AS bf', 'fs.bfid', '=', 'bf.bfid')
            ->join('tbladmindenom AS d', 'fs.adenomid', '=', 'd.adenomid')
            ->leftJoin('tbltransactiontype as tt', 'fd.TransType', 'tt.TTID')
            ->leftJoin('tblcurrency as tc', function($join) {
                $join->on('fd.CurrencyID', '=', 'tc.CurrencyID')
                    ->orOn('bf.CurrencyID', '=', 'tc.CurrencyID');
            })
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.FSStat', 1)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->groupBy('fs.Queued', 'fs.QueuedBy', 'tc.Currency', 'tc.CurrAbbv', 'bf.CurrencyID', 'd.SinagRateBuying', 'fs.CMRUsed', 'fs.Buffer');

        $joined_queries = $branch_stocks_query->unionAll($admin_stocks_query);

        $queued_bills = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('CurrAbbv, CurrencyID, SUM(bill_amount) as total_bill_amount, SUM(bill_count) as total_bill_count, SUM(exchange_amount) as total_exchange_amount, SUM(principal) as total_principal')
            ->selectRaw('SUM(exchange_amount) - SUM(principal) as gain_loss')
            ->groupBy('CurrAbbv', 'CurrencyID')
            ->where('Queued', 1)
            ->whereNotNull('QueuedBy')
            ->get();

        if ($request->ajax()) {
            $html = view('selling_transact_admin.queued_bills', ['test' => $queued_bills])->render();
            return response()->json(['html' => $html, 'test' => $queued_bills]);
        }

        return view('selling_transact_admin.queued_bills')->with('test', $queued_bills);
    }

    public function getBills(Request $request) {
        $selling_rate = $request->get('selling_rate');
        $buffer_val = $request->get('if_buffer') != null ? 1 : 0;

        $currency_ids = explode(", ", trim($request->input('currency_ids')));
        $trimmed_currency_ids = array_map('trim', $currency_ids);

        $selling_rates = explode(", ", trim($request->input('selling_rates')));
        $trimmed_selling_rates = array_map('trim', $selling_rates);

        $branch_stocks_query = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('tc.Currency, fd.CurrencyID, d.SinagRateBuying, fs.Buffer, fs.BufferType, SUM(fs.BillAmount) as bill_amount, COUNT(fs.BillAmount) as bill_count, SUM(fs.BillAmount * d.SinagRateBuying) as principal, GROUP_CONCAT(fs.FSID) as FSIDs, NULL as AFSIDs, 2 as source')
            ->join('tblforexserials AS fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->join('tbldenom AS d', 'fs.DenomID', '=', 'd.DenomID')
            ->join('tbltransactiontype as tt', 'fd.TransType', 'tt.TTID')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            ->whereNull('fs.QueuedBy')
            ->where('fs.Queued', 0)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.Transfer', 1)
            ->whereNotNull('fs.FTDID')
            ->where('fs.Received', 1)
            ->where('fs.FSStat', 2)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->when($request->get('if_buffer') == null, function ($query) use ($request, $trimmed_currency_ids) {
                // return $query->where('fd.CurrencyID', $request->get('currency_id'))
                return $query->when(is_array($trimmed_currency_ids), function ($query) use ($trimmed_currency_ids) {
                    return $query->whereIn('fd.CurrencyID', $trimmed_currency_ids);
                }, function ($query) use ($trimmed_currency_ids) {
                    return $query->where('fd.CurrencyID', $trimmed_currency_ids);
                })
                ->where('fs.Buffer', 0)
                ->where('fd.TransType', $request->get('TTID'));
            })
            ->when($request->get('if_buffer') != null, function ($query) use ($request, $trimmed_currency_ids) {
                // return $query->where('fd.CurrencyID', $request->get('currency_id'))
                    return $query->when(is_array($trimmed_currency_ids), function ($query) use ($trimmed_currency_ids) {
                        return $query->whereIn('fd.CurrencyID', $trimmed_currency_ids);
                    }, function ($query) use ($trimmed_currency_ids) {
                        return $query->where('fd.CurrencyID', $trimmed_currency_ids);
                    })
                    ->where('fs.Buffer', 1)
                    ->where('fd.TransType', 1);
            })
            ->groupBy('tc.Currency', 'fd.CurrencyID', 'd.SinagRateBuying', 'fs.Buffer', 'fs.BufferType');

        $admin_stocks_query = DB::connection('forex')->table('tbladminforexserials AS fs')
            ->selectRaw('tc.Currency, COALESCE(fd.CurrencyID, bf.CurrencyID) as CurrencyID, d.SinagRateBuying, fs.Buffer, fs.BufferType, SUM(fs.BillAmount) as bill_amount, COUNT(fs.BillAmount) as bill_count, SUM(fs.BillAmount * d.SinagRateBuying) as principal, NULL as FSIDs, GROUP_CONCAT(fs.AFSID) as AFSIDs, 1 as source')
            ->leftJoin('tbladminbuyingtransact AS fd', 'fs.aftdid', '=', 'fd.aftdid')
            ->leftJoin('tblbufferfinancing AS bf', 'fs.bfid', '=', 'bf.bfid')
            ->join('tbladmindenom AS d', 'fs.adenomid', '=', 'd.adenomid')
            ->leftJoin('tbltransactiontype as tt', 'fd.TransType', 'tt.TTID')
            ->leftJoin('tblcurrency as tc', function($join) {
                $join->on('fd.CurrencyID', '=', 'tc.CurrencyID')
                     ->orOn('bf.CurrencyID', '=', 'tc.CurrencyID');
            })
            ->whereNull('fs.QueuedBy')
            ->where('fs.Queued', 0)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.FSStat', 1)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->when($request->get('if_buffer') == null, function ($query) use ($request, $trimmed_currency_ids, $buffer_val) {
                if (!empty($trimmed_currency_ids)) {
                    return $query->where('fs.Buffer', 0)
                        ->where('fd.TransType', $request->get('TTID'))
                        ->whereRaw('COALESCE(fd.CurrencyID, bf.CurrencyID) IN (' . $request->input('currency_ids') . ')', $trimmed_currency_ids);
                }   

                return $query;
            })
            ->when($request->get('if_buffer') != null, function ($query) use ($request, $trimmed_currency_ids, $buffer_val) {
                if (!empty($trimmed_currency_ids)) {
                    return $query->where('fs.Buffer', 1)
                        ->where('fd.TransType', $request->get('TTID'))
                        ->whereRaw('COALESCE(fd.CurrencyID, bf.CurrencyID) IN (' . $request->input('currency_ids') . ')', $trimmed_currency_ids);
                }

                return $query;
            })
            // ->when($request->get('if_buffer') == null, function ($query) use ($request) {
            //     return $query->whereRaw('COALESCE(fd.CurrencyID, bf.CurrencyID) = ?', [$request->get('currency_id')])
            //                  ->where('fs.Buffer', 0)
            //                  ->where('fd.TransType', $request->get('TTID'));
            // })
            // ->when($request->get('if_buffer') != null, function ($query) use ($request) {
            //     return $query->whereRaw('COALESCE(fd.CurrencyID, bf.CurrencyID) = ?', [$request->get('currency_id')])
            //                  ->where('fs.Buffer', 1);
            //                 //  ->where('fd.TransType', 1);
            // })
            ->groupBy('tc.Currency', 'CurrencyID', 'd.SinagRateBuying', 'fs.Buffer', 'fs.BufferType');

        $joined_queries = $branch_stocks_query->unionAll($admin_stocks_query);

        $admin_stock_details_s = DB::connection('forex')->query()
            ->fromSub($joined_queries, 'combined')
            ->selectRaw('Currency, CurrencyID, SinagRateBuying, Buffer, BufferType, SUM(bill_amount) as total_bill_amount, SUM(bill_count) as total_bill_count, SUM(principal) as total_principal')
            ->selectRaw('GROUP_CONCAT(FSIDs) as All_FSIDs')
            ->selectRaw('GROUP_CONCAT(AFSIDs) as All_AFSIDs')
            ->groupBy('Currency', 'CurrencyID', 'SinagRateBuying', 'Buffer', 'BufferType')
            ->get();
        
        $selling_rates_map = array_combine($currency_ids, $trimmed_selling_rates);
        
        $admin_stock_details_s = $admin_stock_details_s->map(function ($item) use ($selling_rates_map) {
            $selling_rate = $selling_rates_map[$item->CurrencyID] ?? 0;

            $item->selling_rate = $selling_rate;

            $item->total_exchange_amount = $item->total_bill_amount * $selling_rate;

            $item->gain_loss = ($item->total_bill_amount * $selling_rate) - $item->total_principal ?? NULL;

            return $item;
        });
    
        $response = [
            'admin_stock_details_s' => $admin_stock_details_s
        ];

        return response()->json($response);
    }

    public function queueBills(Request $request) {
        $exploded_fsids = explode(",", trim($request->input('FSIDs')));
        $trimmed_fsids = array_map('trim', $exploded_fsids);

        $exploded_afsids = explode(",", $request->input('AFSIDs'));
        $trimmed_afsids = array_map('trim', $exploded_afsids);

        $exploded_currency_ids = explode(",", trim($request->input('curreny_ids')));
        $currency_ids = array_map('trim', $exploded_currency_ids);

        $exploded_selling_rates = explode(",", trim($request->input('selling_rates')));
        $selling_rates = array_map('trim', $exploded_selling_rates);

        $combined = array_map(function($currency, $rate) {
            return [
                'currency_id' => $currency,
                'selling_rate' => $rate
            ];
        }, $currency_ids, $selling_rates);


        $grouped = [];

        foreach ($combined as $item) {
            $currency_id = $item['currency_id'];
            $selling_rate = (float) $item['selling_rate'];

            if (!isset($grouped[$currency_id])) {
                $grouped[$currency_id] = 0;
            }

            $grouped[$currency_id] = $selling_rate;
        }

        if (!is_null($request->input('FSIDs'))) {
            $rate_case = "CASE fd.CurrencyID ";

            foreach ($grouped as $currency_id => $selling_rate) {
                $rate_case .= "WHEN {$currency_id} THEN {$selling_rate} ";
            }

            $rate_case .= "ELSE fs.CMRUsed END";

            DB::connection('forex')->table('tblforexserials as fs')
                ->selectRaw('fd.CurrencyID, fs.FSID')
                ->join('tblforextransactiondetails as fd', 'fs.FTDID', 'fd.FTDID')
                ->when(is_array($trimmed_fsids), function ($query) use ($trimmed_fsids) {
                    return $query->whereIn('fs.FSID', $trimmed_fsids);
                }, function ($query) use ($trimmed_fsids) {
                    return $query->where('fs.FSID', $trimmed_fsids);
                })
                ->when(!empty($currency_ids), function ($query) use ($currency_ids) {
                    return $query->whereIn('fd.CurrencyID', $currency_ids);
                })->update([
                    'fs.Queued' => 1,
                    'fs.QueuedBy' => Auth::user()->UserID,
                    'fs.CMRUsed' => DB::raw($rate_case)
                ]);
        }

        if (!is_null($request->input('AFSIDs'))) {
            $rate_case = "CASE COALESCE(fd.CurrencyID, bf.CurrencyID) ";

            foreach ($grouped as $currency_id => $selling_rate) {
                $rate_case .= "WHEN {$currency_id} THEN {$selling_rate} ";
            }
    
            $rate_case .= "ELSE fs.CMRUsed END";
    
            DB::connection('forex')->table('tbladminforexserials as fs')
                ->selectRaw('COALESCE(fd.CurrencyID, bf.CurrencyID) as CurrencyID, fs.AFSID')
                ->leftJoin('tbladminbuyingtransact AS fd', 'fs.aftdid', 'fd.aftdid')
                ->leftJoin('tblbufferfinancing AS bf', 'fs.bfid', 'bf.bfid')
                ->when(is_array($trimmed_afsids), function ($query) use ($trimmed_afsids) {
                    return $query->whereIn('fs.AFSID', $trimmed_afsids);
                }, function ($query) use ($trimmed_afsids) {
                    return $query->where('fs.AFSID', $trimmed_afsids);
                })
                ->when(!empty($currency_ids), function ($query) use ($request, $currency_ids) {
                    return $query->whereRaw('COALESCE(fd.CurrencyID, bf.CurrencyID) IN (' . $request->input('curreny_ids') . ')', $currency_ids);
                })->update([
                    'fs.Queued' => 1,
                    'fs.QueuedBy' => Auth::user()->UserID,
                    'fs.CMRUsed' => DB::raw($rate_case)
                ]);
        }

        $response = [];

        return response()->json($response);
    }

    public function unselect(Request $request) {
        $exploded_fsids = explode(",", trim($request->input('FSIDs')));
        $trimmed_fsids = array_map('trim', $exploded_fsids);

        $exploded_afsids = explode(",", trim($request->input('AFSIDs')));
        $trimmed_afsids = array_map('trim', $exploded_afsids);

        if (!is_null($request->input('FSIDs'))) {
            DB::connection('forex')->table('tblforexserials')
                ->when(is_array($trimmed_fsids), function ($query) use ($trimmed_fsids) {
                    return $query->whereIn('tblforexserials.FSID', $trimmed_fsids);
                }, function ($query) use ($trimmed_fsids) {
                    return $query->where('tblforexserials.FSID', $trimmed_fsids);
                })->update([
                    'tblforexserials.Queued' => 0,
                    'tblforexserials.QueuedBy' => null,
                    'tblforexserials.CMRUsed' => 0
                ]);
        }

        if (!is_null($request->input('AFSIDs'))) {
            DB::connection('forex')->table('tbladminforexserials')
                ->when(is_array($trimmed_afsids), function ($query) use ($trimmed_afsids) {
                    return $query->whereIn('tbladminforexserials.AFSID', $trimmed_afsids);
                }, function ($query) use ($trimmed_afsids) {
                    return $query->where('tbladminforexserials.AFSID', $trimmed_afsids);
                })->update([
                    'tbladminforexserials.Queued' => 0,
                    'tbladminforexserials.QueuedBy' => null,
                    'tbladminforexserials.CMRUsed' => 0
                ]);
        }
    }

    public function availableBills(Request $request) {
        $branch_stocks_query = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('fd.CurrencyID, COUNT(fd.CurrencyID) - SUM(CASE WHEN fs.Queued = 1 THEN 1 ELSE 0 END) AS Cnt, SUM(CASE WHEN fs.Queued = 0 THEN fs.BillAmount ELSE 1 END) AS BillAmt, SUM(CASE WHEN fs.Queued = 1 THEN 1 ELSE 0 END) AS Queued, SUM(CASE WHEN fs.Queued = 1 THEN fs.BillAmount ELSE 0 END) AS AmountQ, SUM(fs.BillAmount * d.SinagRateBuying) AS Principal')
            ->join('tblforexserials AS fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->join('tbldenom AS d', 'fs.DenomID', '=', 'd.DenomID')
            // ->where('fs.Buffer', 0)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.Transfer', 1)
            ->whereNotNull('fs.FTDID')
            ->where('fs.Received', 1)
            ->where('fs.FSStat', 2)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->groupBy('fd.CurrencyID');

        $admin_stocks_query = DB::connection('forex')->table('tbladminforexserials AS fs')
            ->selectRaw('COALESCE(fd.CurrencyID, bf.CurrencyID) as CurrencyID, COUNT(COALESCE(fd.CurrencyID, bf.CurrencyID)) - SUM(CASE WHEN fs.Queued = 1 THEN 1 ELSE 0 END) AS Cnt, SUM(CASE WHEN fs.Queued = 0 THEN fs.BillAmount ELSE 0 END) AS BillAmt, SUM(CASE WHEN fs.Queued = 1 THEN 1 ELSE 0 END) AS Queued, SUM(CASE WHEN fs.Queued = 1 THEN fs.BillAmount ELSE 0 END) AS AmountQ, SUM(fs.BillAmount * d.SinagRateBuying) AS Principal')
            ->leftJoin('tbladminbuyingtransact AS fd', 'fs.aftdid', '=', 'fd.aftdid')
            ->leftJoin('tblbufferfinancing AS bf', 'fs.bfid', '=', 'bf.bfid')
            ->join('tbladmindenom AS d', 'fs.adenomid', '=', 'd.adenomid')
            // ->where('fs.Buffer', 0)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.FSStat', 1)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->groupBy('CurrencyID');

        $available_serials = DB::connection('forex')->table('tblcurrency AS c')
            ->leftJoinSub($branch_stocks_query, 'bs', function($join) {
                $join->on('c.currencyid', '=', 'bs.CurrencyID');
            })
            ->leftJoinSub($admin_stocks_query, 'ad', function($join) {
                $join->on('c.currencyid', '=', 'ad.CurrencyID');
            })
            ->selectRaw('c.CurrencyID, c.Currency, c.CurrAbbv,
                IFNULL(bs.Cnt, 0) + IFNULL(ad.Cnt, 0) AS total_bill_count,
                IFNULL(bs.BillAmt, 0) + IFNULL(ad.BillAmt, 0) AS total_curr_amount,
                IFNULL(bs.Queued, 0) + IFNULL(ad.Queued, 0) AS queued_total_bill_count,
                IFNULL(bs.AmountQ, 0) + IFNULL(ad.AmountQ, 0) AS queued_total_curr_amnt,
                IFNULL(bs.Principal, 0) + IFNULL(ad.Principal, 0) AS total_principal'
            )
            ->havingRaw('total_principal > 0')
            // ->havingRaw('total_curr_amount > 0')
            ->orderBy('c.currency')
            ->get();

        $response = [
            'available_serials' => $available_serials
        ];

        return response()->json($response);
    }

    public function addBuffRate(Request $request) {
        $trimmed_fsids = [];

        foreach ($request->get('FSIDs') as $values) {
            if ($values) {
                $trimmed_fsids = array_merge($trimmed_fsids, explode(',', $values));
            }
        }

        $trimmed_afsids = [];

        foreach ($request->get('AFSIDs') as $values) {
            if ($values) {
                $trimmed_afsids = array_merge($trimmed_afsids, explode(',', $values));
            }
        }

        if (count($trimmed_fsids)) {
            DB::connection('forex')->table('tblforexserials as fs')
                ->when(is_array($trimmed_fsids), function ($query) use ($trimmed_fsids) {
                    return $query->whereIn('fs.FSID', $trimmed_fsids);
                }, function ($query) use ($trimmed_fsids) {
                    return $query->where('fs.FSID', $trimmed_fsids);
                })->update([
                    'fs.CMRUsed' => $request->get('buffer_rate')
                ]);
        }

        if (count($trimmed_afsids)) {
            DB::connection('forex')->table('tbladminforexserials as fs')
                ->when(is_array($trimmed_afsids), function ($query) use ($trimmed_afsids) {
                    return $query->whereIn('fs.AFSID', $trimmed_afsids);
                }, function ($query) use ($trimmed_afsids) {
                    return $query->where('fs.AFSID', $trimmed_afsids);
                })->update([
                    'fs.CMRUsed' => $request->get('buffer_rate')
                ]);
        }
    }

    public function sell(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        return view('selling_transact_admin.selling_transact_admin', compact('menu_id'));
    }

    public function queued(Request $request) {
        $r_set = $request->input('r_set');

        $branch_stocks_query = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('accounting.tcd.CompanyID, accounting.tcd.CompanyName, tc.Currency, fd.CurrencyID, fs.CMRUsed, d.SinagRateBuying, SUM(fs.BillAmount) as bill_amount, COUNT(fs.BillAmount) as bill_count, SUM(fs.BillAmount * fs.CMRUsed) as exchange_amount, SUM(fs.BillAmount * d.SinagRateBuying) as principal, GROUP_CONCAT(fs.FSID) as FSIDs, NULL as AFSIDs, 2 as source, sl.Active, sl.Limit, fd.Rset, fs.Buffer, COALESCE(bt.BufferType, fs.BufferType) as BufferType, bt.Received')
            ->join('tblforexserials AS fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->leftJoin('tblbuffertransfer as bt', 'fs.BufferID', 'bt.BufferID')
            ->join('tbldenom AS d', 'fs.DenomID', '=', 'd.DenomID')
            ->join('tbltransactiontype as tt', 'fd.TransType', 'tt.TTID')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            ->join('tblbranch as tb', 'fd.BranchID', 'tb.BranchID')
            ->join('pawnshop.tblxbranch as tbx', 'tb.BranchCode', 'pawnshop.tbx.BranchCode')
            ->join('accounting.tblsegmentgroup as sgt', 'pawnshop.tbx.BranchID', 'accounting.sgt.BranchID')
            ->join('accounting.tblcompany as tcd', 'accounting.sgt.CompanyID', 'accounting.tcd.CompanyID')
            ->join('accounting.tblsegments as sgs', 'accounting.sgt.SegmentID', 'accounting.sgs.SegmentID')
            ->join('tblsellinglimit as sl', 'accounting.tcd.CompanyID', 'sl.CompanyID')
            // ->where('fs.Buffer', 0)
            ->where('sgs.SegmentID', '=', 3)
            ->whereNotNull('fs.QueuedBy')
            ->where('fs.Queued', 1)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.Transfer', 1)
            ->whereNotNull('fs.FTDID')
            ->where('fs.Received', 1)
            ->where('fs.FSStat', 2)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            // ->where('fd.TRset', $r_set)
            ->where('fd.Rset', $r_set)
            ->groupBy('accounting.tcd.CompanyID', 'accounting.tcd.CompanyName', 'tc.Currency', 'fd.CurrencyID', 'fs.CMRUsed', 'd.SinagRateBuying', 'sl.Active', 'sl.Limit', 'fd.Rset', 'fs.Buffer', 'BufferType', 'bt.Received');

        $admin_stocks_query = DB::connection('forex')->table('tbladminforexserials AS fs')
            ->selectRaw('accounting.tcd.CompanyID, accounting.tcd.CompanyName, tc.Currency, COALESCE(fd.CurrencyID, bf.CurrencyID) as CurrencyID, fs.CMRUsed, d.SinagRateBuying, SUM(fs.BillAmount) as bill_amount, COUNT(fs.BillAmount) as bill_count, SUM(fs.BillAmount * fs.CMRUsed) as exchange_amount, SUM(fs.BillAmount * d.SinagRateBuying) as principal, NULL as FSIDs, GROUP_CONCAT(fs.AFSID) as AFSIDs, 1 as source, sl.Active, sl.Limit, COALESCE(fd.Rset, bf.Rset) as Rset, fs.Buffer, COALESCE(bt.BufferType, fs.BufferType) as BufferType, bt.Received')
            ->leftJoin('tbladminbuyingtransact AS fd', 'fs.aftdid', '=', 'fd.aftdid')
            ->leftJoin('tblbufferfinancing AS bf', 'fs.bfid', '=', 'bf.bfid')
            ->join('tbladmindenom AS d', 'fs.adenomid', '=', 'd.adenomid')
            ->leftJoin('tblbuffertransfer as bt', 'fs.BufferID', 'bt.BufferID')
            ->leftJoin('tbltransactiontype as tt', 'fd.TransType', 'tt.TTID')
            // ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            // ->join('tblbranch as tb', 'fd.BranchID', 'tb.BranchID')
            ->leftJoin('tblcurrency as tc', function($join) {
                $join->on('fd.CurrencyID', '=', 'tc.CurrencyID')
                     ->orOn('bf.CurrencyID', '=', 'tc.CurrencyID');
            })
            ->leftJoin('tblbranch as tb', function($join) {
                $join->on('fd.BranchID', '=', 'tb.BranchID')
                     ->orOn('bf.BranchID', '=', 'tb.BranchID');
            })
            ->join('pawnshop.tblxbranch as tbx', 'tb.BranchCode', 'pawnshop.tbx.BranchCode')
            ->join('accounting.tblsegmentgroup as sgt', 'pawnshop.tbx.BranchID', 'accounting.sgt.BranchID')
            ->join('accounting.tblcompany as tcd', 'accounting.sgt.CompanyID', 'accounting.tcd.CompanyID')
            ->join('accounting.tblsegments as sgs', 'accounting.sgt.SegmentID', 'accounting.sgs.SegmentID')
            ->join('tblsellinglimit as sl', 'accounting.tcd.CompanyID', 'sl.CompanyID')
            // ->where('fs.Buffer', 0)
            ->where('sgs.SegmentID', '=', 3)
            ->whereNotNull('fs.QueuedBy')
            ->where('fs.Queued', 1)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.FSStat', 1)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->whereRaw('COALESCE(fd.Rset, bf.Rset) = ? ', $r_set)
            ->groupBy('accounting.tcd.CompanyID', 'accounting.tcd.CompanyName', 'tc.Currency', 'CurrencyID', 'fs.CMRUsed', 'd.SinagRateBuying', 'sl.Active', 'sl.Limit', 'Rset', 'fs.Buffer', 'BufferType', 'bt.Received');

        $joined_queries = $branch_stocks_query->unionAll($admin_stocks_query);

        $bills_rset = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('CompanyID, CompanyName, Currency, CurrencyID, CMRUsed, SinagRateBuying, SUM(bill_amount) as total_bill_amount, SUM(bill_count) as total_bill_count, SUM(exchange_amount) as total_exchange_amount, SUM(principal) as total_principal, Rset, Buffer, BufferType, Received')
            ->selectRaw('GROUP_CONCAT(FSIDs) as All_FSIDs')
            ->selectRaw('GROUP_CONCAT(AFSIDs) as All_AFSIDs')
            ->selectRaw('SUM(exchange_amount) - SUM(principal) as gain_loss')
            ->groupBy('CompanyID', 'CompanyName', 'Currency', 'CurrencyID', 'CMRUsed', 'SinagRateBuying', 'Rset', 'Buffer', 'BufferType', 'Received')
            ->orderBy('CompanyID', 'ASC')
            ->get();

        $no_rates = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('MAX(CASE WHEN CMRUsed = 0 THEN 1 ELSE 0 END) as has_no_rate')
            ->value('has_no_rate');

        $total_sales_per_company = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('CompanyID, CompanyName, SUM(exchange_amount) as total_exchange_amount, Active')
            ->groupBy('CompanyID', 'CompanyName', 'Active')
            ->orderBy('CompanyID', 'ASC')
            ->get();

        $sales_limit = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->select('CompanyID', 'Limit')
            ->where('Active', '=', 1)
            ->groupBy('CompanyID', 'Limit')
            ->orderBy('CompanyID', 'ASC')
            ->get();

        $response = [
            'no_rates' => $no_rates,
            'bills_rset' => $bills_rset,
            'total_sales_per_company' => $total_sales_per_company,
            'sales_limit' => $sales_limit
        ];

        return response()->json($response);
    }

    public function save(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');
        $company_ids = $request->input('company-id');
        $curr_ids = $request->input('currency-id');
        $total_bill_amnt = $request->input('total-bill-amnt');
        $rate_used = $request->input('rate-used');
        $selling_rate = $request->input('selling-rate');
        $exchange_amnt = $request->input('exchange-amnt');
        $principal_amnt = $request->input('principal-amnt');
        $gain_loss = $request->input('gain-loss');
        $excluded_amnt = $request->input('excluded-amnt');
        $buffer_only_amnt = $request->input('buffer-only-amnt');
        // $excluded_add_funds_amnt = $request->input('total-excluded-add-funds');

        // $actual_add_funds = 0;

        // if (floatval($excluded_add_funds_amnt) != 0) {
        //     $actual_add_funds += $request->input('total-generated-ex-ex-r-input') - $excluded_add_funds_amnt;
        // } else {
        //     $actual_add_funds += $request->input('total-generated-ex-ex-r-input');
        // }

        $STMDID = '';
        $TCID = '';

        $get_selling_to_mnl_no = DB::connection('forex')->table('tblsoldtomaniladetails')
            ->selectRaw('MAX(STMNo) + 1 AS latest_stm_no')
            ->value('latest_stm_no');

        $latest_stmno = $get_selling_to_mnl_no == null ? 1 : $get_selling_to_mnl_no;

        $STMDID = DB::connection('forex')->table('tblsoldtomaniladetails')
            ->insertGetId([
                'STMNo' => $latest_stmno,
                'TotalExchangeAmount' => $request->input('total-generated-ex-ex-r-input'),
                'TotalPrincipal' => $request->input('total-generated-capital-input'),
                'TotalGainLoss' => $request->input('total-generated-gain-loss-input'),
                'UserID' => $request->input('matched_user_id'),
                'CustomerID' => $request->input('customer-id-selected'),
                'RSet' => $request->input('radio-rset'),
                'Remarks' => $request->input('remarks'),
                'DateSold' => $raw_date->toDateString(),
                'TimeSold' => $raw_date->toTimeString(),
            ]);

        $max_afno = DB::connection('generaldcpr')->table('tbladdfunds')
            ->selectRaw('CASE WHEN MAX(afno) IS NULL THEN 1 ELSE MAX(afno) + 1 END AS maxAFNo')
            ->value('maxAFNo');

        // if ($actual_add_funds > 0) {
            DB::connection('generaldcpr')->table('tbladdfunds')
                ->insert([
                    'entrydate' => $raw_date->toDateString(),
                    'entrytime' => $raw_date->toTimeString(),
                    'afno' => $max_afno,
                    'afdate' => $raw_date->toDateString(),
                    'acctidd' => 1196,
                    'acctidc' => 1733,
                    'orno' => 0,
                    'amount' => $request->input('total-generated-ex-ex-r-input'),
                    // 'amount' => $actual_add_funds,
                    'remarks' => 'PALIT INCOME INCLUDED',
                    'branchid' => 1,
                    'appraiserid' => Auth::user()->UserID,
                    'userid' => $request->input('matched_user_id'),
                    'SGID' => 2,
                    'SegmentID' => 3,
                ]);
        // }

        foreach ($company_ids as $key => $company_id) {
            $latest_fc_form_series = DB::connection('forex')->table('tblfcformseries')
                ->where('tblfcformseries.RSet', '=', $request->input('radio-rset'))
                ->where('tblfcformseries.CompanyID', '=', $company_id)
                ->selectRaw('MAX(FormSeries) + 1 AS latest_fc_form_series')
                ->value('latest_fc_form_series');

            $new_fc_form_series[$company_id] = $latest_fc_form_series;

            DB::connection('forex')->table('tblsoldbillstomanila')
                ->insert([
                    'STMDID' => $STMDID,
                    'CompanyID' => $company_id,
                    'CurrencyID' => $curr_ids[$key],
                    'CurrAmount' => str_replace(',', '', $total_bill_amnt[$key]),
                    'SinagRateBuying' => floatval($rate_used[$key]),
                    'CMRUsed' => floatval($selling_rate[$key]),
                    'ExchangeAmount' => str_replace(',', '', $exchange_amnt[$key]),
                    'PrincipalAmount' => str_replace(',', '', $principal_amnt[$key]),
                    'GainLoss' => str_replace(',', '', $gain_loss[$key]),
                    'FormSeries' => $new_fc_form_series[$company_id],
                    'DateSold' => $raw_date->toDateString(),
                    'TimeSold' =>  $raw_date->toTimeString(),
                ]);
        }

        $exploded_fsids = explode(",", trim($request->input('FSIDs')));
        $trimmed_fsids = array_map('trim', $exploded_fsids);

        $exploded_afsids = explode(",", trim($request->input('AFSIDs')));
        $trimmed_afsids = array_map('trim', $exploded_afsids);

        if (!is_null($request->input('FSIDs'))) {
            $update_bills = DB::connection('forex')->table('tblforexserials')
                ->when(is_array($trimmed_fsids), function ($query) use ($trimmed_fsids) {
                    return $query->whereIn('tblforexserials.FSID', $trimmed_fsids);
                }, function ($query) use ($trimmed_fsids) {
                    return $query->where('tblforexserials.FSID', $trimmed_fsids);
                });

            $update_bills->clone()
                ->update([
                    'tblforexserials.Queued' => 0,
                    'tblforexserials.QueuedBy' => null,
                    'tblforexserials.SoldToManila' => 1,
                    'tblforexserials.STMDID' => $STMDID,
                ]);

            // $branch_sales = DB::connection('forex')->table('tblforexserials as fs')
            //     ->selectRaw('fs.Buffer, fs.BufferType, fd.BranchID, bt.Received, SUM(fs.BillAmount * fs.CMRUsed) as trans_cap_amnt')
            //     ->leftJoin('tblbuffertransfer as bt', 'fs.TFID', 'bt.TFID')
            //     ->join('tblforextransactiondetails as fd', 'fs.FTDID', 'fd.FTDID')
            //     ->when(is_array($trimmed_fsids), function ($query) use ($trimmed_fsids) {
            //         return $query->whereIn('fs.FSID', $trimmed_fsids);
            //     }, function ($query) use ($trimmed_fsids) {
            //         return $query->where('fs.FSID', $trimmed_fsids);
            //     })
            //     ->groupBy('fs.Buffer', 'fs.BufferType', 'fd.BranchID', 'bt.Received')
            //     ->get();

            $init_query = DB::connection('forex')->table('tblforexserials as fs')
                ->when(is_array($trimmed_fsids), function ($query) use ($trimmed_fsids) {
                    return $query->whereIn('fs.FSID', $trimmed_fsids);
                }, function ($query) use ($trimmed_fsids) {
                    return $query->where('fs.FSID', $trimmed_fsids);
                });

            $BufferID = $init_query->clone()               
                ->groupBy('fs.BufferID')
                ->pluck('fs.BufferID')
                ->toArray();

            $branch_sales = DB::connection('forex')->table('tblforexserials as fs')
                ->selectRaw('COALESCE(bt.BufferType, fs.BufferType) as BufferType, COALESCE(bt.BranchID, fd.BranchID) as BranchID, fs.Buffer, CASE WHEN bt.Received IS NULL OR bt.Received = 0 THEN 0 WHEN bt.Received = 1 THEN 1 END as Received, SUM(fs.BillAmount * fs.CMRUsed) as trans_cap_amnt')
                // ->leftJoin('tblforexserials as fs', 'bt.BufferID'D, 'fs.BufferID')
                ->leftJoin('tblbuffertransfer as bt', 'fs.BufferID', 'bt.BufferID')
                ->join('tblforextransactiondetails as fd', 'fs.FTDID', 'fd.FTDID')
                ->when(is_array($trimmed_fsids), function ($query) use ($trimmed_fsids) {
                    return $query->whereIn('fs.FSID', $trimmed_fsids);
                }, function ($query) use ($trimmed_fsids) {
                    return $query->where('fs.FSID', $trimmed_fsids);
                })
                ->when(!empty($BufferID), function ($query) use ($BufferID) {
                    $filteredBufferID = array_filter($BufferID, fn($id) => !is_null($id));
                    return $query->where(function ($subQuery) use ($filteredBufferID) {
                        $subQuery->whereIn('fs.BufferID', $filteredBufferID)
                            ->orWhere(function ($innerQuery) {
                                $innerQuery->whereNull('fs.BufferID')
                                    ->where('fs.Buffer', '<>', '1');
                            });
                    });
                })
                // ->when(!empty($BufferID) && array_filter($BufferID, fn($id) => !is_null($id)), function ($query) use ($BufferID) {
                //     $filteredBufferID = array_filter($BufferID, fn($id) => !is_null($id));
                //     return $query->whereIn('fs.BufferID', $filteredBufferID);
                // })
                ->groupBy('BufferType', 'BranchID', 'fs.Buffer', 'Received')
                ->get();

            // dd($branch_sales);

            $get_tc_no = DB::connection('forex')->table('tblfxtranscap')
                ->selectRaw('MAX(TCNo) + 1 AS max_tc_no')
                ->value('max_tc_no');

            $trans_cap_connection = DB::connection('forex')->table('tblfxtranscap');

            foreach ($branch_sales as $sales) {
                if ($sales->Buffer == 0) {
                    $trans_cap_connection->insert([
                        'Transferred' => 1,
                        'STMDID' => $STMDID,
                        'TCNo' => $get_tc_no++,
                        'BranchID' => $sales->BranchID,
                        'TranscapAmount' => $sales->trans_cap_amnt,
                        'UserID' => $request->input('matched_user_id')
                    ]);
                } else if ($sales->Buffer == 1) {
                    if ($sales->BufferType == 1 && $sales->Received == 0) {
                        $trans_cap_connection->insert([
                            'Transferred' => 1,
                            'STMDID' => $STMDID,
                            'TCNo' => $get_tc_no++,
                            'BranchID' => $sales->BranchID,
                            'TranscapAmount' => $sales->trans_cap_amnt,
                            'UserID' => $request->input('matched_user_id')
                        ]);
                    }
                }
            }
        }

        if (!is_null($request->input('AFSIDs'))) {
            $update_bills_admin = DB::connection('forex')->table('tbladminforexserials')
                ->when(is_array($trimmed_afsids), function ($query) use ($trimmed_afsids) {
                    return $query->whereIn('tbladminforexserials.AFSID', $trimmed_afsids);
                }, function ($query) use ($trimmed_afsids) {
                    return $query->where('tbladminforexserials.AFSID', $trimmed_afsids);
                });

            $update_bills_admin->clone()
                ->update([
                    'tbladminforexserials.Queued' => 0,
                    'tbladminforexserials.QueuedBy' => null,
                    'tbladminforexserials.SoldToManila' => 1,
                    'tbladminforexserials.STMDID' => $STMDID,
                ]);

            // $admin_sales = DB::connection('forex')->table('tbladminforexserials as fs')
            //     ->selectRaw('fs.Buffer, fs.BufferType, COALESCE(fd.BranchID, bf.BranchID) as BranchID, SUM(fs.BillAmount * fs.CMRUsed) as trans_cap_amnt')
            //     ->leftJoin('tbladminbuyingtransact as fd', 'fs.AFTDID', 'fd.AFTDID')
            //     ->leftJoin('tblbufferfinancing AS bf', 'fs.bfid', '=', 'bf.bfid')
            //     ->leftJoin('tblbuffertransfer as bt', 'bf.BranchID', 'bt.BranchID')
            //     ->join('tbladmindenom AS d', 'fs.adenomid', '=', 'd.adenomid')
            //     ->when(is_array($trimmed_afsids), function ($query) use ($trimmed_afsids) {
            //         return $query->whereIn('fs.AFSID', $trimmed_afsids);
            //     }, function ($query) use ($trimmed_afsids) {
            //         return $query->where('fs.AFSID', $trimmed_afsids);
            //     })
            //     ->groupBy('fs.Buffer', 'fs.BufferType', 'BranchID')
            //     ->get();

            $init_query = DB::connection('forex')->table('tbladminforexserials as fs')
                ->when(is_array($trimmed_afsids), function ($query) use ($trimmed_afsids) {
                    return $query->whereIn('fs.AFSID', $trimmed_afsids);
                }, function ($query) use ($trimmed_afsids) {
                    return $query->where('fs.AFSID', $trimmed_afsids);
                });

            $BufferID = $init_query->clone()               
                ->groupBy('fs.BufferID')
                ->pluck('fs.BufferID')
                ->toArray();

            $admin_sales = DB::connection('forex')->table('tbladminforexserials as fs')
                ->selectRaw('COALESCE(bt.BufferType, fs.BufferType) as BufferType, COALESCE(bt.BranchID, fd.BranchID) as BranchID, fs.Buffer, CASE WHEN bt.Received IS NULL OR bt.Received = 0 THEN 0 WHEN bt.Received = 1 THEN 1 END as Received, SUM(fs.BillAmount * fs.CMRUsed) as trans_cap_amnt')
                // ->leftJoin('tblforexserials as fs', 'bt.BufferID'D, 'fs.BufferID')
                ->leftJoin('tblbuffertransfer as bt', 'fs.BufferID', 'bt.BufferID')
                ->leftJoin('tbladminbuyingtransact as fd', 'fs.AFTDID', 'fd.AFTDID')
                ->when(is_array($trimmed_afsids), function ($query) use ($trimmed_afsids) {
                    return $query->whereIn('fs.AFSID', $trimmed_afsids);
                }, function ($query) use ($trimmed_afsids) {
                    return $query->where('fs.AFSID', $trimmed_afsids);
                })
                ->when(!empty($BufferID), function ($query) use ($BufferID) {
                    $filteredBufferID = array_filter($BufferID, fn($id) => !is_null($id));
                    return $query->where(function ($subQuery) use ($filteredBufferID) {
                        $subQuery->whereIn('fs.BufferID', $filteredBufferID)
                            ->orWhere(function ($innerQuery) {
                                $innerQuery->whereNull('fs.BufferID')
                                    ->where('fs.Buffer', '<>', '1');
                            });
                    });
                })
                // ->when(!empty($BufferID) && array_filter($BufferID, fn($id) => !is_null($id)), function ($query) use ($BufferID) {
                //     $filteredBufferID = array_filter($BufferID, fn($id) => !is_null($id));
                //     return $query->whereIn('fs.BufferID', $filteredBufferID);
                // })
                ->groupBy('BufferType', 'BranchID', 'fs.Buffer', 'Received')
                ->get();

            $get_tc_no = DB::connection('forex')->table('tblfxtranscap')
                ->selectRaw('MAX(TCNo) + 1 AS max_tc_no')
                ->value('max_tc_no');

            $trans_cap_connection = DB::connection('forex')->table('tblfxtranscap');

            foreach ($admin_sales as $sales) {
                if ($sales->Buffer == 0) {
                    $trans_cap_connection->insert([
                        'Transferred' => 1,
                        'STMDID' => $STMDID,
                        'TCNo' => $get_tc_no++,
                        'BranchID' => $sales->BranchID,
                        'TranscapAmount' => $sales->trans_cap_amnt,
                        'UserID' => $request->input('matched_user_id')
                    ]);
                } else if ($sales->Buffer == 1) {
                    if ($sales->BufferType == 1 && $sales->Received == 0) {
                        $trans_cap_connection->insert([
                            'Transferred' => 1,
                            'STMDID' => $STMDID,
                            'TCNo' => $get_tc_no++,
                            'BranchID' => $sales->BranchID,
                            'TranscapAmount' => $sales->trans_cap_amnt,
                            'UserID' => $request->input('matched_user_id')
                        ]);
                    }
                }
            }
        }

        if (isset($excluded_amnt) && count($excluded_amnt) > 0) {
            $max_bcno = DB::connection('forex')->table('tblbuffercontrol')
                ->selectRaw('MAX(BCNO) + 1 AS maxBCNO')
                ->value('maxBCNO');

            DB::connection('forex')->table('tblbuffercontrol')
                ->insert([
                    'BCNO' => $max_bcno,
                    'BCDate' => $raw_date->toDateString(),
                    'BCType' => 2,
                    'DOTID' => 2,
                    'DollarOut' => array_sum($buffer_only_amnt) - array_sum($excluded_amnt),
                    'Balance' => 0,
                    'UserID' => $request->input('matched_user_id'),
                    'EntryDate' => $raw_date->toDateTimeString(),
                    'BranchID' => Auth::user()->getBranch()->BranchID,
                ]);
        }
        
        // if (isset($buffer_only_amnt)) {
        //     $max_bcno = DB::connection('forex')->table('tblbuffercontrol')
        //         ->selectRaw('MAX(BCNO) + 1 AS maxBCNO')
        //         ->value('maxBCNO');

        //     DB::connection('forex')->table('tblbuffercontrol')
        //         ->insert([
        //             'BCNO' => $max_bcno,
        //             'BCDate' => $raw_date->toDateString(),
        //             'BCType' => 2,
        //             'DOTID' => 2,
        //             'DollarOut' => array_sum($buffer_only_amnt),
        //             'Balance' => 0,
        //             'UserID' => $request->input('matched_user_id'),
        //             'EntryDate' => $raw_date->toDateTimeString(),
        //             'BranchID' => Auth::user()->getBranch()->BranchID,
        //         ]);
        // }

        $grouped_company_ids = DB::connection('forex')->table('tblfcformseries')
            ->where('tblfcformseries.RSet', '=', $request->input('radio-rset'))
            ->when(is_array($company_ids), function ($query) use ($company_ids) {
                return $query->whereIn('tblfcformseries.CompanyID', $company_ids);
            }, function ($query) use ($company_ids) {
                return $query->where('tblfcformseries.CompanyID', $company_ids);
            })
            ->select('tblfcformseries.CompanyID', 'tblfcformseries.FormSeries')
            ->get();

        foreach ($grouped_company_ids as $value) {
            DB::connection('forex')->table('tblfcformseries')
                ->where('tblfcformseries.RSet', '=', $request->input('radio-rset'))
                ->where('tblfcformseries.CompanyID', '=', $value->CompanyID)
                ->update([
                    'FormSeries' => $value->FormSeries + 1
                ]);
        } 

        $trans_cap_details = DB::connection('forex')->table('tblfxtranscap as tc')
            ->selectRaw('tc.STMDID, tc.TCID, tc.TranscapAmount, txb.BranchID')
            ->join('tblbranch as tb', 'tc.BranchID', 'tb.BranchID')
            ->join('pawnshop.tblxbranch as txb', 'tb.BranchCode', 'txb.BranchCode')
            ->where('tc.STMDID', $STMDID)
            ->groupBy('tc.STMDID', 'tc.TCID', 'tc.TranscapAmount', 'txb.BranchID')
            ->get();

        $get_tr_no = DB::connection('generaldcpr')->table('tbltransaddcap')
            ->selectRaw('MAX(trno) + 1 AS max_tr_no')
            ->value('max_tr_no');

        foreach ($trans_cap_details as $details) {
            DB::connection('generaldcpr')->table('tbltransaddcap')
                ->insert([
                    'tentrydate' => $raw_date->toDateString(),
                    'tentrytime' => $raw_date->toTimeString(),
                    'trno' => $get_tr_no++,
                    'trdate' => $raw_date->toDateString(),
                    'tramount' => $details->TranscapAmount,
                    'tobranchid' => $details->BranchID,
                    'tremarks' => 'TRANSCAP - FOREX',
                    'tbranchid' => 1,
                    'tappraiserid' => Auth::user()->UserID,
                    'tuserid' => $request->input('matched_user_id'),
                    'STMDID' => $STMDID,
                    'TCID' => $details->TCID,
                    'SegmentID' => 3,
                ]);
        }

        $id = $STMDID;

        return response()->json(['id' => $id]);
    }

    public function cappedBills(Request $request) {
        $parsed_company_id = explode(", ", $request->input('parsed_company_id'));

        $branch_stocks_query = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('fs.FSID, fd.CurrencyID, tc.Currency, fs.BillAmount, fs.Serials, fs.CMRUsed, accounting.tcd.CompanyID, accounting.tcd.CompanyName, sl.Limit as selling_limit, 2 as source_type')
            ->join('tblforexserials AS fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->join('tbldenom AS d', 'fs.DenomID', '=', 'd.DenomID')
            ->join('tbltransactiontype as tt', 'fd.TransType', 'tt.TTID')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            ->join('tblbranch as tb', 'fd.BranchID', 'tb.BranchID')
            ->join('pawnshop.tblxbranch as tbx', 'tb.BranchCode', 'pawnshop.tbx.BranchCode')
            ->join('accounting.tblsegmentgroup as sgt', 'pawnshop.tbx.BranchID', 'accounting.sgt.BranchID')
            ->join('accounting.tblcompany as tcd', 'accounting.sgt.CompanyID', 'accounting.tcd.CompanyID')
            ->join('accounting.tblsegments as sgs', 'accounting.sgt.SegmentID', 'accounting.sgs.SegmentID')
            ->join('tblsellinglimit as sl', 'accounting.tcd.CompanyID', 'sl.CompanyID')
            ->when(is_array($parsed_company_id), function ($query) use ($parsed_company_id) {
                return $query->whereIn('fd.CompanyID', $parsed_company_id);
            }, function ($query) use ($parsed_company_id) {
                return $query->where('fd.CompanyID', $parsed_company_id);
            })
            // ->where('fs.Buffer', 0)
            ->where('sgs.SegmentID', '=', 3)
            ->where('sl.Active', '=', 1)
            ->whereNotNull('fs.QueuedBy')
            ->where('fs.Queued', 1)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.Transfer', 1)
            ->whereNotNull('fs.FTDID')
            ->where('fs.Received', 1)
            ->where('fs.FSStat', 2)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->where('fd.Rset', $request->input('r_set'))
            ->groupBy('fs.FSID', 'fd.CurrencyID', 'tc.Currency', 'fs.BillAmount', 'fs.Serials', 'fs.CMRUsed', 'accounting.tcd.CompanyID', 'accounting.tcd.CompanyName', 'selling_limit');

        $admin_stocks_query = DB::connection('forex')->table('tbladminbuyingtransact AS fd')
            ->selectRaw('fs.AFSID, fd.CurrencyID, tc.Currency, fs.BillAmount, fs.Serials, fs.CMRUsed, accounting.tcd.CompanyID, accounting.tcd.CompanyName, sl.Limit as selling_limit, 1 as source_type')
            ->join('tbladminforexserials AS fs', 'fd.aftdid', '=', 'fs.aftdid')
            ->join('tbladmindenom AS d', 'fs.adenomid', '=', 'd.adenomid')
            ->join('tbltransactiontype as tt', 'fd.TransType', 'tt.TTID')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            ->join('tblbranch as tb', 'fd.BranchID', 'tb.BranchID')
            ->join('pawnshop.tblxbranch as tbx', 'tb.BranchCode', 'pawnshop.tbx.BranchCode')
            ->join('accounting.tblsegmentgroup as sgt', 'pawnshop.tbx.BranchID', 'accounting.sgt.BranchID')
            ->join('accounting.tblcompany as tcd', 'accounting.sgt.CompanyID', 'accounting.tcd.CompanyID')
            ->join('accounting.tblsegments as sgs', 'accounting.sgt.SegmentID', 'accounting.sgs.SegmentID')
            ->join('tblsellinglimit as sl', 'accounting.tcd.CompanyID', 'sl.CompanyID')
            ->when(is_array($parsed_company_id), function ($query) use ($parsed_company_id) {
                return $query->whereIn('fd.CompanyID', $parsed_company_id);
            }, function ($query) use ($parsed_company_id) {
                return $query->where('fd.CompanyID', $parsed_company_id);
            })
            // ->where('fs.Buffer', 0)
            ->where('sgs.SegmentID', '=', 3)
            ->where('sl.Active', '=', 1)
            ->whereNotNull('fs.QueuedBy')
            ->where('fs.Queued', 1)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.FSStat', 1)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->where('fd.Rset', $request->input('r_set'))
            ->groupBy('fs.AFSID', 'fd.CurrencyID', 'tc.Currency', 'fs.BillAmount', 'fs.Serials', 'fs.CMRUsed', 'accounting.tcd.CompanyID', 'accounting.tcd.CompanyName', 'selling_limit');

        $joined_queries = $branch_stocks_query->unionAll($admin_stocks_query);

        $by_company = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('CompanyID, CompanyName, COUNT(BillAmount) AS bill_count, selling_limit')
            ->groupBy('CompanyID', 'CompanyName', 'selling_limit')
            ->orderBy('CompanyID', 'ASC')
            ->get();

        $by_serial = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('FSID as ID, CompanyID, CurrencyID, Currency, BillAmount, Serials, CMRUsed, SUM(BillAmount * CMRUsed) as exchange_amount, source_type')
            ->groupBy('ID', 'CompanyID', 'CurrencyID', 'Currency', 'BillAmount', 'Serials', 'CMRUsed', 'source_type')
            ->orderBy('Currency', 'ASC')
            ->orderBy('BillAmount', 'DESC')
            ->get();

        $grouped_by_company = $by_serial->groupBy('CompanyID');

        $over_cap_companies = $by_company->map(function ($company) use ($grouped_by_company) {
            $company->Serials = $grouped_by_company->get($company->CompanyID, collect());
            return $company;
        });

        $reponse = [
            // 'currencies' => $currencies,
            'over_cap_companies' => $over_cap_companies
        ];

        return response()->json($reponse);
    }

    public function unqueueCappedBills(Request $request) {
        $exploded_fsids = explode(",", $request->input('FSIDs'));
        $exploded_afsids = explode(",", $request->input('AFSIDs'));

        if (!is_null($request->input('FSIDs'))) {
            DB::connection('forex')->table('tblforexserials')
                ->when(is_array($exploded_fsids), function ($query) use ($exploded_fsids) {
                    return $query->whereIn('tblforexserials.FSID', $exploded_fsids);
                }, function ($query) use ($exploded_fsids) {
                    return $query->where('tblforexserials.FSID', $exploded_fsids);
                })
                ->update([
                    'tblforexserials.Queued' => 0,
                    'tblforexserials.QueuedBy' => null,
                    'tblforexserials.CMRUsed' => 0
                ]);
        }

        if (!is_null($request->input('AFSIDs'))) {
            DB::connection('forex')->table('tbladminforexserials as fasx')
                ->when(is_array($exploded_afsids), function ($query) use ($exploded_afsids) {
                    return $query->whereIn('fasx.AFSID', $exploded_afsids);
                }, function ($query) use ($exploded_afsids) {
                    return $query->where('fasx.AFSID', $exploded_afsids);
                })
                ->update([
                    'fasx.Queued' => 0,
                    'fasx.QueuedBy' => null,
                    'fasx.CMRUsed' => 0
                ]);
        }

        $success = true;

        return response()->json($success);
    }

    public function details(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['selling_trans_details'] = DB::connection('forex')->table('tblsoldtomaniladetails')
            ->join('tblsoldbillstomanila', 'tblsoldtomaniladetails.STMDID', 'tblsoldbillstomanila.STMDID')
            ->join('accounting.tblcompany', 'tblsoldbillstomanila.CompanyID', 'accounting.tblcompany.CompanyID')
            ->join('tblcurrency', 'tblsoldbillstomanila.CurrencyID', 'tblcurrency.CurrencyID')
            ->where('tblsoldbillstomanila.STMDID', '=', $request->id)
            ->selectRaw('accounting.tblcompany.CompanyID, accounting.tblcompany.CompanyName, tblsoldbillstomanila.FormSeries')
            ->groupBy('accounting.tblcompany.CompanyID', 'accounting.tblcompany.CompanyName', 'tblsoldbillstomanila.FormSeries')
            ->get();

        $STMDIDs = [];

        foreach ($result['selling_trans_details'] as $index => $selling_trans_details) {
            $get_currencies_query = DB::connection('forex')->table('tblsoldtomaniladetails')
                ->join('tblsoldbillstomanila', 'tblsoldtomaniladetails.STMDID', 'tblsoldbillstomanila.STMDID')
                ->join('tblcurrency', 'tblsoldbillstomanila.CurrencyID', 'tblcurrency.CurrencyID')
                ->where('tblsoldbillstomanila.STMDID', '=', $request->id)
                ->where('tblsoldbillstomanila.CompanyID', '=', $selling_trans_details->CompanyID)
                ->selectRaw('tblsoldbillstomanila.CurrencyID, tblcurrency.Currency, tblsoldbillstomanila.CMRUsed, SUM(tblsoldbillstomanila.CurrAmount) as total_curr_amount')
                ->groupBy('tblsoldbillstomanila.CurrencyID', 'tblcurrency.Currency', 'tblsoldbillstomanila.CMRUsed')
                ->orderBy('tblcurrency.Currency', 'ASC')
                ->get();

            $currency_ids = [];

            foreach ($get_currencies_query as $get_currency_ids) {
                $currency_ids[] = $get_currency_ids;
            }

            $selling_trans_details->Currency = $currency_ids;

            $STMDIDs[] = $selling_trans_details;
        }

        $result['bills_sold_to_mnl'] = DB::connection('forex')->table('tblsoldtomaniladetails')
            ->join('pawnshop.tblxusers', 'tblsoldtomaniladetails.UserID', 'pawnshop.tblxusers.UserID')
            ->join('pawnshop.tblxcustomer' , 'tblsoldtomaniladetails.CustomerID' , 'pawnshop.tblxcustomer.CustomerID')
            ->where('tblsoldtomaniladetails.STMDID', '=', $request->id)
            ->select(
                'tblsoldtomaniladetails.STMNo',
                'tblxcustomer.FullName',
                'tblsoldtomaniladetails.RSet',
                'tblsoldtomaniladetails.DateSold',
                'tblsoldtomaniladetails.TimeSold',
                'tblsoldtomaniladetails.TotalExchangeAmount',
                'tblsoldtomaniladetails.TotalPrincipal',
                'tblsoldtomaniladetails.TotalGainLoss',
            )
            ->get();

        $STMDID = $request->id;

        return view('selling_transact_admin.selling_transact_details_admin', compact('result', 'STMDID', 'menu_id'));
    }

    public function print(Request $request) {
        $selling_gar = DB::connection('forex')->table('tblsoldtomaniladetails')
            ->join('pawnshop.tblxusers', 'tblsoldtomaniladetails.UserID', 'pawnshop.tblxusers.UserID')
            ->join('pawnshop.tblxcustomer', 'tblsoldtomaniladetails.CustomerID', 'pawnshop.tblxcustomer.CustomerID')
            ->join('tblsoldbillstomanila', 'tblsoldtomaniladetails.STMDID', 'tblsoldbillstomanila.STMDID')
            ->join('accounting.tblcompany', 'tblsoldbillstomanila.CompanyID', 'accounting.tblcompany.CompanyID')
            ->join('tblcurrency', 'tblsoldbillstomanila.CurrencyID', 'tblcurrency.CurrencyID')
            ->where('tblsoldbillstomanila.STMDID', '=', $request->get('STMDID'))
            ->where('tblsoldbillstomanila.CompanyID', '=', $request->get('company_id'))
            ->selectRaw('accounting.tblcompany.CompanyID, accounting.tblcompany.CompanyName, pawnshop.tblxcustomer.FullName, pawnshop.tblxcustomer.Address2, tblsoldtomaniladetails.DateSold, pawnshop.tblxusers.Name, pawnshop.tblxcustomer.Nameofemployer, tblsoldbillstomanila.FormSeries, tblsoldtomaniladetails.Rset')
            ->groupBy('accounting.tblcompany.CompanyID', 'accounting.tblcompany.CompanyName', 'pawnshop.tblxcustomer.FullName', 'pawnshop.tblxcustomer.Address2', 'tblsoldtomaniladetails.DateSold', 'pawnshop.tblxusers.Name', 'pawnshop.tblxcustomer.Nameofemployer', 'tblsoldbillstomanila.FormSeries', 'tblsoldtomaniladetails.Rset')
            ->get();

        $STMDIDs = [];

        foreach ($selling_gar as $index => $selling_trans_details) {
            $get_currencies_query = DB::connection('forex')->table('tblsoldtomaniladetails')
                ->join('tblsoldbillstomanila', 'tblsoldtomaniladetails.STMDID', 'tblsoldbillstomanila.STMDID')
                ->join('tblcurrency', 'tblsoldbillstomanila.CurrencyID', 'tblcurrency.CurrencyID')
                ->where('tblsoldbillstomanila.STMDID', '=', $request->STMDID)
                ->where('tblsoldbillstomanila.CompanyID', '=', $request->get('company_id'))
                ->selectRaw('tblsoldbillstomanila.CurrencyID, tblcurrency.Currency, tblsoldbillstomanila.CMRUsed, SUM(tblsoldbillstomanila.CurrAmount) as total_curr_amount')
                ->groupBy('tblsoldbillstomanila.CurrencyID', 'tblcurrency.Currency', 'tblsoldbillstomanila.CMRUsed')
                ->orderBy('tblcurrency.Currency', 'ASC')
                ->get();

            $currency_ids = [];

            foreach ($get_currencies_query as $get_currency_ids) {
                $currency_ids[] = $get_currency_ids;
            }

            $selling_trans_details->Currency = $currency_ids;

            $STMDIDs[] = $selling_trans_details;
        }

        if ($request->ajax()) {
            $html = view('selling_transact_admin.s_trans_details_per_company', ['test' => $STMDIDs])->render();
            return response()->json(['html' => $html, 'test' => $STMDIDs]);
        }

        return view('selling_transact_admin.s_trans_details_per_company')->with('test', $STMDIDs);
    }
}
