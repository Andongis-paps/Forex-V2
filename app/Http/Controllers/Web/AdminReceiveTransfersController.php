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

class AdminReceiveTransfersController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:RECEIVE TRANSFER FOREX,VIEW')->only(['show', 'details', 'incoming']);
        $this->middleware('check.access.permission:RECEIVE TRANSFER FOREX,ADD')->only(['add', 'save', 'search']);
        $this->middleware('check.access.permission:RECEIVE TRANSFER FOREX,EDIT')->only(['unreceiveBills']);
        $this->middleware('check.access.permission:RECEIVE TRANSFER FOREX,DELETE')->only(['unreceive']);
    }

    public function show(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['received_transfers'] = DB::connection('forex')->table('tblreceivetransfer as trt')
            ->select('tfx.TransferForexID AS TransferForexID', 'tfx.TransferForexNo AS TransferForexNo', 'tfx.TransferDate AS TransferDate', 'tfx.ITNo AS TrackingNo', 'tfx.Remarks AS TransfeRemarks', 'trt.Received AS ReceivedStatus', 'trt.RTDate AS ReceivedDate', 'trt.Remarks AS ReceivedRemarks', 'trt.UserID AS ReceivedBy', 'pawnshop.tblxusers.Name AS Name', 'tb.BranchCode AS BranchCode', 'trt.RTID AS RTID')
            ->join('tbltransferforex as tfx', 'trt.TFID', 'tfx.TransferForexID')
            ->join('tblbranch as tb', 'tfx.BranchID', 'tb.BranchID')
            ->join('pawnshop.tblxusers', 'trt.UserID', '=', 'pawnshop.tblxusers.UserID')
            ->whereBetween('trt.RTDate', ['2023-01-01', $raw_date->toDateString()])
            ->where('tfx.Remarks', '!=', 'BUFFER')
            ->whereNotNull('tfx.ITNo')
            ->orderBy('trt.RTID', 'DESC')
            ->paginate(25, ['*'], 'received');

        $result['transfers'] = DB::connection('forex')->table('tbltransferforex as tfx')
            ->select('tfx.TransferForexID AS TFID', 'tfx.TransferForexNo AS TFNO', 'tfx.ITNo AS TrackingNo', 'tfx.TransferDate AS TFDate', 'tfx.BranchID AS TFBranch', 'tfx.Remarks AS TFRemarks', 'tfx.EntryDate AS TFEntryDate', 'tfx.CourierID AS TFCourierID', 'tfr.RTID AS RTID', 'tfr.TFID AS RTTFID', 'tfr.RTDate AS RTDate', 'tfr.UserID AS RTReceivedBy', 'tfr.Received AS RTReceived', 'tfr.Remarks AS RTRemarks', 'tb.BranchCode AS BranchCode')
            ->join('tblbranch as tb', 'tfx.BranchID', 'tb.BranchID')
            ->leftJoin('tblreceivetransfer as tfr', 'tfx.TransferForexID', 'tfr.TFID')
            ->whereBetween('tfx.TransferDate', ['2025-01-01', $raw_date->toDateString()])
            ->where('tfr.Received', '=', null)
            ->where('tfx.Remarks', '!=', 'BUFFER')
            ->where('tfx.Voided', '=', 0)
            ->whereNotNull('tfx.ITNo')
            ->orderBy('tfx.TransferForexNo' , 'DESC')
            ->paginate(25, ['*'], 'icoming');

        return view('selling_receive_transfers.received_transfers', compact('result', 'menu_id'));
    }

    public function add(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        return view('selling_receive_transfers.receive_transfers', compact('menu_id'));
    }

    public function dupeSerials(Request $request) {
        $TFIDS = explode(",", trim($request->get('TFIDS')));
        $TFIDs = array_map('trim', $TFIDS);

        $query = DB::connection('forex')->table('tblforexserials as fs')
            ->when(is_array($TFIDs), function ($query) use ($TFIDs) {
                    return $query->whereIn('fs.TFID', $TFIDs);
                }, function ($query) use ($TFIDs) {
                    return $query->where('fs.TFID', $TFIDs);
                });

        $exploded_serials = $query->clone()
            ->pluck('fs.Serials')
            ->toArray();

        $branch_serials = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('fs.Serials')
            ->join('tblforexserials AS fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->when(is_array($exploded_serials), function ($query) use ($exploded_serials) {
                return $query->whereIn('Serials', $exploded_serials);
            }, function ($query) use ($exploded_serials) {
                return $query->where('Serials', $exploded_serials);
            })
            ->where('fs.Transfer', 1)
            ->where('fs.Received', 1)
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->groupBy('fs.Serials', 'fs.FSID', 'fs.SoldToManila');

        $admin_serials = DB::connection('forex')->table('tbladminforexserials AS fs')
            ->selectRaw('fs.Serials')
            ->leftJoin('tbladminbuyingtransact AS fd', 'fs.aftdid', '=', 'fd.aftdid')
            ->leftJoin('tblbufferfinancing AS bf', 'fs.bfid', '=', 'bf.bfid')
            ->when(is_array($exploded_serials), function ($query) use ($exploded_serials) {
                return $query->whereIn('Serials', $exploded_serials);
            }, function ($query) use ($exploded_serials) {
                return $query->where('Serials', $exploded_serials);
            })
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->groupBy('fs.Serials', 'fs.AFSID', 'fs.SoldToManila');

        $joined_queries = $branch_serials->unionAll($admin_serials);

        $serial_stocks = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('Serials');
            
        $boolean = $serial_stocks->clone()
            ->exists();

        $dupe_serials = $serial_stocks->clone()
            ->get();

        $response = [
            'boolean' => $boolean,
            'dupe_serials' => $dupe_serials
        ];

        return response()->json($response);
    }

    public function save(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        $TFIDS_received = explode(",", trim($request->input('received-transfers-tfid')));
        $TFIDs_received = array_map('trim', $TFIDS_received);

        $query = DB::connection('forex')->table('tblforexserials as fs')
            ->when(is_array($TFIDs_received), function ($query) use ($TFIDs_received) {
                return $query->whereIn('fs.TFID', $TFIDs_received);
            }, function ($query) use ($TFIDs_received) {
                return $query->where('fs.TFID', $TFIDs_received);
            });

        foreach ($TFIDs_received as $key => $value) {
            $TFID = DB::connection('forex')->table('tblreceivetransfer')
                ->insertGetId([
                    'TFID' => $value,
                    'EntryDate' => $raw_date->toDateTimeString(),
                    'RTDate' => $raw_date->toDateString(),
                    'UserID' => $request->get('matched_user_id'),
                    'Received' => 1,
                ]);

            $query->clone()->update([
                    'Received' => 1
                ]);
        }

        // $FTDIDs = $query->clone()->select('fs.FTDID')
        //     ->groupBy('fs.FTDID')
        //     ->pluck('FTDID')
        //     ->toArray();

        // foreach ($FTDIDs as $FTDID) {
        //     DB::connection('forex')->select('CALL spj_Company_Transaction_Splitting(?)', [$FTDID]);
        // }
    }

    public function details(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['transfer_forex'] = DB::connection('forex')->table('tbltransferforex')
            ->join('tblbranch', 'tbltransferforex.BranchID', '=', 'tblbranch.BranchID')
            ->where('tbltransferforex.TransferForexID', '=', $request->id)
            ->get();

        $query = DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            ->join('tblforexserials as fs', 'fd.FTDID', 'fs.FTDID')
            ->join('tbltransferforex as tfx', 'fs.TFID', 'tfx.TransferForexID')
            // ->join('tbltransferforexdetails as fxd', 'fs.FSID', 'fxd.FSID')
            ->where('fs.Sold', '=', 0)
            ->where('fs.FSStat', '=', 2)
            ->where('fs.Transfer', '=', 1)
            ->where('tfx.Remarks', '!=', 'BUFFER')
            ->where('fs.TFID', '=', $request->id);

        $result['serial_count_per_currency'] = $query->clone()
            ->selectRaw('tc.Currency, SUM(fs.BillAmount) as total_bill_amount, COUNT(fs.BillAmount) as bill_amount_count')
            ->groupBy('tc.Currency')
            ->orderBy('tc.Currency', 'ASC')
            ->get();

        $result['serial_breakdown'] = $query->clone()
            ->selectRaw('fs.BillAmount, tc.Currency, SUM(fs.BillAmount) as total_bill_amount, COUNT(fs.BillAmount) as bill_amount_count')
            ->groupBy('fs.BillAmount', 'tc.Currency')
            ->orderBy('tc.Currency', 'ASC')
            ->get();

        // $result['transfer_forex_deet'] = DB::connection('forex')->table('tbltransferforexdetails as fxd')
        //     ->selectRaw('fs.FSID, fs.Serials, fs.BillAmount, tblcurrency.Currency')
        //     ->join('tblforexserials as fs', 'fxd.FSID', 'fs.FSID')
        //     ->join('tbltransferforex as tfx', 'fxd.TransferForexID', 'tfx.TransferForexID')
        //     ->join('tblforextransactiondetails as fd', 'fs.FTDID', 'fd.FTDID')
        //     ->join('tblcurrency', 'fd.CurrencyID', 'tblcurrency.CurrencyID')
        //     ->where('fs.Sold', '=', 0)
        //     ->where('fs.FSStat', '=', 2)
        //     ->where('fs.Transfer', '=', 1)
        //     ->where('tfx.Remarks', '!=', 'BUFFER')
        //     ->where('fs.TFID', '=', $request->id)
        //     ->groupBy('fs.FSID', 'fs.Serials', 'fs.BillAmount', 'tblcurrency.Currency')
        //     ->orderBy('tblcurrency.Currency', 'ASC')
        //     ->orderBy('fs.BillAmount', 'DESC')
        //     ->paginate(15);

        $result['transfer_forex_deet'] = $query->clone()
            ->selectRaw('fd.ORNo, fd.TransactionDate, fs.FSID, fs.Serials, fs.BillAmount, tc.Currency')
            ->groupBy('fd.ORNo', 'fd.TransactionDate','fs.FSID', 'fs.Serials', 'fs.BillAmount', 'tc.Currency')
            ->orderBy('tc.Currency', 'ASC')
            ->orderBy('fs.BillAmount', 'DESC')
            ->paginate(15);

        $result['bill_status'] = DB::connection('forex')->table('tblbillstatus')
            ->get();

        $result['transfer_fx_id'] = $request->id;

        return view('selling_receive_transfers.received_transf_details', compact('result', 'menu_id'));
    }

    public function unreceiveBills(Request $request) {
        $bill_tags_array = $request->input('bill-tags');
        $serials = $request->input('parsed_unreceive_serials');
        $parsed_serials = explode(',', $serials);

        DB::connection('forex')->table('tblforexserials')
            ->when(is_array($parsed_serials), function ($query) use ($parsed_serials) {
                return $query->whereIn('tblforexserials.FSID', $parsed_serials);
            }, function ($query) use ($parsed_serials) {
                return $query->where('tblforexserials.FSID', $parsed_serials);
            })
            ->update([
                'Transfer' => 0,
                'Received' => 0,
                'TFID' => 0,
                'TFUID' => 0,
                'FSStat' => 1,
            ]);
    }

    public function unreceive(Request $request) {
        DB::connection('forex')->table('tblforexserials')
            ->where('tblforexserials.TFID', '=', $request->input('TFXID'))
            ->update([
                'Received' => 0
            ]);

        DB::connection('forex')->table('tblreceivetransfer')
            ->where('tblreceivetransfer.RTID', '=', $request->input('RTID'))
            ->delete();
    }

    public function incoming(Request $request) {
        $query = DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            ->join('tblforexserials as fs', 'fd.FTDID', 'fs.FTDID')
            ->join('tbltransferforex as tfx', 'fs.TFID', 'tfx.TransferForexID')
            ->where('fs.Sold', '=', 0)
            ->where('fs.FSStat', '=', 2)
            ->where('fs.Transfer', '=', 1)
            ->where('tfx.Remarks', '!=', 'BUFFER')
            ->where('fs.TFID', '=', $request->input('TFXID'));

        $transfer_per_currency = $query->clone()
            ->selectRaw('tc.Currency, SUM(fs.BillAmount) as total_bill_amount, COUNT(fs.BillAmount) as bill_amount_count')
            ->groupBy('tc.Currency')
            ->orderBy('tc.Currency', 'ASC')
            ->get();

        $transfer_per_serial = $query->clone()
            ->selectRaw('fd.TransactionDate, fd.ORNo, fs.FSID, fs.Serials, fs.BillAmount, tc.Currency')
            ->groupBy('fd.TransactionDate', 'fd.ORNo', 'fs.FSID', 'fs.Serials', 'fs.BillAmount', 'tc.Currency')
            ->orderBy('tc.Currency', 'ASC')
            ->orderBy('fs.BillAmount', 'DESC')
            ->get();

        $response = [
            'transfer_per_currency' => $transfer_per_currency,
            'transfer_per_serial' => $transfer_per_serial
        ];

        return response()->json($response);
    }

    public function search(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');
        $search_type = $request->input('search_type');
        $parsed_transf_fx_no = $request->get('parsed_transf_fx_no');
        $transf_forex_no_parsed = explode(',', $parsed_transf_fx_no);
        $parsed_tracking_no = $request->get('parsed_tracking_no');
        $tracking_no_parsed = explode(',', $parsed_tracking_no);

        $transfer_forex_search = [];

        $test = DB::connection('forex')->table('tbltransferforex')
            ->select(
                'tbltransferforex.TransferForexID AS TFID',
                'tbltransferforex.ITNo AS TrackingNo',
                'tbltransferforex.TransferForexNo AS TFNO',
                'tbltransferforex.TransferDate AS TFDate',
                'tbltransferforex.BranchID AS TFBranch',
                'tbltransferforex.Remarks AS TFRemarks',
                DB::raw('DATE(tbltransferforex.EntryDate) AS TFEntryDate'),
                // 'tblreceivetransfer.RTID AS RTID',
                // 'tblreceivetransfer.TFID AS RTTFID',
                // 'tblreceivetransfer.RTDate AS RTDate',
                // 'tblreceivetransfer.UserID AS RTReceivedBy',
                'tblreceivetransfer.Received AS RTReceived',
                // 'tblreceivetransfer.Remarks AS RTRemarks',
                'tblbranch.BranchCode AS BranchCode',
            )
            ->join('tblbranch', 'tbltransferforex.BranchID', 'tblbranch.BranchID')
            ->leftJoin('tblreceivetransfer', 'tbltransferforex.TransferForexID', 'tblreceivetransfer.TFID')
            ->whereNull('tblreceivetransfer.Received')
            // ->whereBetween('tbltransferforex.TransferDate', ['2025-01-01', $raw_date->toDateString()])
            ->whereNotNull('tbltransferforex.ITNo')
            ->where('tbltransferforex.Voided', '=', 0)
            ->where('tbltransferforex.Remarks', '!=', 'DPOFX')
            ->where('tbltransferforex.Remarks', '!=', 'BUFFER')
            ->orderBy('tbltransferforex.TransferForexID', 'DESC');

        switch ($search_type) {
            case 1:
                $transfer_forex_search = $test->where('tbltransferforex.Remarks', '=', $request->get('transfer_type'))
                    ->get();
                break;

            case 2:
                $transfer_forex_search = $test->when(is_array($transf_forex_no_parsed), function ($query) use ($transf_forex_no_parsed) {
                        return $query->whereIn('tbltransferforex.TransferForexNo', $transf_forex_no_parsed);
                    }, function ($query) use ($transf_forex_no_parsed) {
                        return $query->where('tbltransferforex.TransferForexNo', $transf_forex_no_parsed);
                    })
                    ->get();
                break;

            case 3:
                $transfer_forex_search = $test->when(is_array($tracking_no_parsed), function ($query) use ($tracking_no_parsed) {
                        return $query->whereIn('tbltransferforex.ITNo', $tracking_no_parsed);
                    }, function ($query) use ($tracking_no_parsed) {
                        return $query->where('tbltransferforex.ITNo', $tracking_no_parsed);
                    })
                    ->get();
                // $transfer_forex_search = $test->where('tbltransferforex.ITNo', '=', $request->get('tracking_no'))
                //     ->get();
                break;

            default:
                dd("no transactions available!");
        }

        $response = [
            'transfer_forex_search' => $transfer_forex_search
        ];

        return response()->json($response);
    }

    // public function transferForexSearch(Request $request) {
    //     $raw_date = Carbon::now('Asia/Manila');
    //     $parsed_transf_fx_no = $request->get('parsed_transf_fx_no');
    //     $transf_forex_no_parsed = explode(',', $parsed_transf_fx_no);

    //     $transfer_forex_search = [];

    //     $test = DB::connection('forex')->table('tbltransferforex')
    //         ->select(
    //             'tbltransferforex.TransferForexID AS TFID',
    //             'tbltransferforex.ITNo AS TrackingNo',
    //             'tbltransferforex.TransferForexNo AS TFNO',
    //             'tbltransferforex.TransferDate AS TFDate',
    //             'tbltransferforex.BranchID AS TFBranch',
    //             'tbltransferforex.Remarks AS TFRemarks',
    //             DB::raw('DATE(tbltransferforex.EntryDate) AS TFEntryDate'),
    //             'tblreceivetransfer.RTID AS RTID',
    //             'tblreceivetransfer.TFID AS RTTFID',
    //             'tblreceivetransfer.RTDate AS RTDate',
    //             'tblreceivetransfer.UserID AS RTReceivedBy',
    //             'tblreceivetransfer.Received AS RTReceived',
    //             'tblreceivetransfer.Remarks AS RTRemarks',
    //             'tblbranch.BranchCode AS BranchCode',
    //         )
    //         ->join('tblbranch', 'tbltransferforex.BranchID', 'tblbranch.BranchID')
    //         ->leftJoin('tblreceivetransfer', 'tbltransferforex.TransferForexID', 'tblreceivetransfer.TFID')
    //         ->whereBetween('tbltransferforex.TransferDate', ['2025-01-01', $raw_date->toDateString()])
    //         ->where('tblreceivetransfer.Received', '=', null);

    //     if ($request->get('search_type') == 1) {
    //         $transfer_forex_search = $test->where('tblreceivetransfer.Received', '=', null)
    //             ->where('tbltransferforex.Remarks', '=', $request->get('transfer_type'))
    //             ->orderBy('tbltransferforex.TransferDate', 'DESC')
    //             ->get();
    //     } else if ($request->get('search_type') == 2) {
    //         $transfer_forex_search = $test->where('tblreceivetransfer.Received', '=', null)
    //             ->when(is_array($transf_forex_no_parsed), function ($query) use ($transf_forex_no_parsed) {
    //                 return $query->whereIn('tbltransferforex.TransferForexNo', $transf_forex_no_parsed);
    //             }, function ($query) use ($transf_forex_no_parsed) {
    //                 return $query->where('tbltransferforex.TransferForexNo', $transf_forex_no_parsed);
    //             })
    //             ->where('tbltransferforex.Remarks', '!=', 'DPOFX')
    //             ->where('tbltransferforex.Remarks', '!=', 'BUFFER')
    //             ->orderBy('tbltransferforex.TransferDate', 'DESC')
    //             ->get();
    //     } else if ($request->get('search_type') == 3) {
    //         $transfer_forex_search = $test->where('tblreceivetransfer.Received', '=', null)
    //             ->where('tbltransferforex.ITNo', '=', $request->get('tracking_.no'))
    //             ->where('tbltransferforex.Remarks', '!=', 'DPOFX')
    //             ->where('tbltransferforex.Remarks', '!=', 'BUFFER')
    //             // ->where()
    //             ->orderBy('tbltransferforex.TransferDate', 'DESC')
    //             ->get();
    //     }

    //     $response = [
    //         'transfer_forex_search' => $transfer_forex_search
    //     ];

    //     return response()->json($response);
    // }

    // public function receivedBillsTagging(Request $request) {
    //     $transfer_fx_id = $request->input('transfer_fx_id');
    //     $serials = $request->input('parsed_serials');
    //     $bill_amnt = $request->input('parsed_bill_amount');
    //     $fsid = $request->input('parsed_unreceive_serials');

    //     $parsed_fsid = explode(',', $fsid);
    //     $parsed_serials = explode(',', $serials);
    //     $parsed_bill_amnt = explode(',', $bill_amnt);

    //     $bill_tags = [];

    //     foreach ($request->all() as $key => $value) {
    //         if (strpos($key, 'bill-tags-') === 0) {
    //             $bill_tags[$key] = $value;
    //         }
    //     }

    //     foreach ($parsed_fsid as $key => $fsid) {
    //         $tags_index = 'bill-tags' .'-'.trim($fsid);

    //         DB::connection('forex')->table('tbltransferredbillstagging')
    //             ->insert([
    //                 'TFID' => $transfer_fx_id,
    //                 'FSID' => $fsid,
    //                 'Serials' => trim($parsed_serials[$key]),
    //                 'BillAmount' => $parsed_bill_amnt[$key],
    //                 'WithStain' => in_array(1, $bill_tags[$tags_index]) ? 1 : 0,
    //                 'Folded' => in_array(2, $bill_tags[$tags_index]) ? 1 : 0,
    //                 'Demonetized' => in_array(3, $bill_tags[$tags_index]) ? 1 : 0,
    //                 'Fake' => in_array(4, $bill_tags[$tags_index]) ? 1 : 0,
    //                 'Faded' => in_array(5, $bill_tags[$tags_index]) ? 1 : 0,
    //                 'MissingBill' => in_array(6, $bill_tags[$tags_index]) ? 1 : 0,
    //                 'MissingPart' => in_array(7, $bill_tags[$tags_index]) ? 1 : 0,
    //                 'Crumpled' => in_array(8, $bill_tags[$tags_index]) ? 1 : 0
    //             ]);
    //     }
    // }

    // public function untaggingReceivedBills(Request $request) {
    //     $TBTIDs = $request->input('parsed_tbtid');
    //     $parsed_tbtids = explode(',', $TBTIDs);

    //     DB::connection('forex')->table('tbltransferredbillstagging')
    //         ->when(is_array($parsed_tbtids), function ($query) use ($parsed_tbtids) {
    //             return $query->whereIn('tbltransferredbillstagging.TBTID', $parsed_tbtids);
    //         }, function ($query) use ($parsed_tbtids) {
    //             return $query->where('tbltransferredbillstagging.TBTID', $parsed_tbtids);
    //         })
    //         ->delete();
    // }
}
