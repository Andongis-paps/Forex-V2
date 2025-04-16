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
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class AdminBillTaggingController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:BILL TAGGING,VIEW')->only(['show']);
        $this->middleware('check.access.permission:BILL TAGGING,ADD')->only(['search', 'save', 'saveATDEmp']);
        $this->middleware('check.access.permission:BILL TAGGING,EDIT')->only(['edit', 'print']);
        $this->middleware('check.access.permission:BILL TAGGING,DELETE')->only(['untag']);
    }

    public function show(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['tagged_bills'] = DB::connection('forex')->table('tbltaggedbills as tb')
            ->selectRaw('tb.FrontBillImage, tb.BackBillImage, COALESCE(fs.FSID, afs.AFSID) as  ID, tb.TBTID, tc.CurrAbbv, tc.Currency, tbr.BranchID, tbr.BranchCode, tx.BranchID as TXBranchID, tb.BillAmount, tb.SellingRate, tb.ATDAmount, tb.Serials, stm.STMNo, stm.DateSold, tb.ATDNo, hris.tbllogin.UserID as HRUserID, tb.EmployeeID, tb.UserID, hris.tbllogin.FullName, DATE(tb.DateAdded) as DateAdded, dc.DNO')
            ->leftJoin('tblforexserials as fs', 'tb.FSID', 'fs.FSID')
            ->leftJoin('tblforextransactiondetails as fd', 'tb.FTDID', 'fd.FTDID')
            ->leftJoin('tbladminforexserials as afs', 'tb.AFSID', 'afs.AFSID')
            ->leftJoin('tblbufferfinancing as bf', 'afs.BFID', 'bf.BFID')
            ->leftJoin('tblsoldtomaniladetails as stm', 'tb.STMDID', 'stm.STMDID')
            ->leftJoin('hris.tbllogin', 'tb.EmployeeID', 'hris.tbllogin.UserID')
            ->leftJoin('hrissinag.tbldiscrepancydetails as dcd', 'tb.TBTID', 'dcd.TBTID')
            ->leftJoin('hrissinag.tbldiscrepancy as dc', 'dcd.DID', 'dc.DID')
            ->leftJoin('tblcurrency as tc', function($join) {
                $join->on('fd.CurrencyID', '=', 'tc.CurrencyID')
                     ->orOn('bf.CurrencyID', '=', 'tc.CurrencyID');
            })
            ->leftJoin('tblbranch as tbr', function($join) {
                $join->on('fd.BranchID', '=', 'tbr.BranchID')
                     ->orOn('bf.BranchID', '=', 'tbr.BranchID');
            })
            ->join('pawnshop.tblxbranch as tx', 'tbr.BranchCode', 'tx.BranchCode')
            ->groupBy('tb.FrontBillImage', 'tb.BackBillImage', 'ID', 'tb.TBTID', 'tc.CurrAbbv', 'tc.Currency', 'tbr.BranchID', 'tbr.BranchCode', 'TXBranchID', 'tb.BillAmount', 'tb.SellingRate', 'tb.ATDAmount', 'tb.Serials', 'stm.STMNo', 'stm.DateSold', 'tb.ATDNo', 'HRUserID', 'tb.EmployeeID', 'tb.UserID', 'hris.tbllogin.FullName', 'DateAdded', 'dc.DNO')
            ->orderBy('tb.TBTID', 'DESC')
            ->paginate(20);

        $TBTIDs = [];

        foreach ($result['tagged_bills'] as $index => $tagged_bills) {
            $tagged_bills_details = DB::connection('forex')->table('tbltaggedbillsdetails')
                ->select('tbltaggedbillsdetails.BillStatID')
                ->where('tbltaggedbillsdetails.TBTID', '=', $tagged_bills->TBTID)
                ->orderBy('tbltaggedbillsdetails.TBDID', 'DESC')
                ->get();

            $bill_stat_ids = [];

            foreach ($tagged_bills_details as $get_bill_stat_ids) {
                $bill_stat_ids[] = $get_bill_stat_ids;
            }

            $tagged_bills->BillTags = $bill_stat_ids;

            $TBTIDs[] = $tagged_bills;
        }

        $result['bill_tags'] = DB::connection('forex')->table('tblbillstatus')
            ->get();

        return view('bill_tagging_admin.bill_tagging_admin', compact('result', 'menu_id'));
    }

    public function employees(Request $request) {
        $appraisers = DB::connection('hris')->table('tbllogin as tl')
            ->select('tl.UserID', 'tl.FullName', 'tbx.BranchID')
            ->join('pawnshop.tblxbranch as tbx', 'tl.Branch', 'tbx.BranchID')
            ->join('forex.tblbranch as tb', 'tbx.BranchCode', 'tb.BranchCode')
            ->whereIn('tl.PositionID', [3, 18, 25])
            ->where('tl.Branch', intval($request->get('tbx_branch_id')))
            ->where('tl.IsActive', "TRUE")
            ->orderBy('tl.FullName', 'ASC')
            ->get();

        $response = [
            'appraisers' => $appraisers
        ];

        return response()->json($response);
    }

    public function search(Request $request) {
        $branch_stocks_query = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('fs.FSID, tc.Currency, fs.Serials, fs.BillAmount, fs.CMRUsed, tb.BranchCode, stm.DateSold, fd.FTDID, stm.STMDID, 2 as source_type, null as BFID')
            ->join('tblforexserials AS fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->join('tblbranch as tb', 'fd.BranchID', 'tb.BranchID')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            // ->join('tbldenom AS d', 'fs.DenomID', '=', 'd.DenomID')
            ->join('tbltransactiontype as tt', 'fd.TransType', '=', 'tt.TTID')
            ->leftJoin('tbltaggedbills as tgb', 'fs.FSID', '=', 'tgb.FSID')
            ->join('tblsoldtomaniladetails as stm', 'fs.STMDID', '=', 'stm.STMDID')
            // ->where('fs.Sold', '=', 0)
            // ->where('fs.Transfer', '=', 1)
            ->where('fs.Received', '=', 1)
            // ->where('fs.Queued', '=', 1)
            ->where('fs.SoldToManila', '=', 1)
            ->whereNotNull('fs.STMDID')
            ->where('fs.FSStat', '=', 2)
            ->where('fs.FSType', 1)
            ->whereNull('tgb.FSID')
            ->groupBy('fs.FSID', 'tc.Currency', 'fs.Serials', 'fs.BillAmount', 'fs.CMRUsed', 'tb.BranchCode', 'stm.DateSold', 'fd.FTDID', 'stm.STMDID');

        $admin_stocks_query = DB::connection('forex')->table('tbladminforexserials AS fs')
        // $admin_stocks_query = DB::connection('forex')->table('tbladminbuyingtransact AS fd')
            ->selectRaw('fs.AFSID, tc.Currency, fs.Serials, fs.BillAmount, fs.CMRUsed, tb.BranchCode, stm.DateSold, fd.AFTDID, stm.STMDID, 1 as source_type, bf.BFID')
            ->leftJoin('tbladminbuyingtransact AS fd', 'fs.AFTDID', '=', 'fd.AFTDID')
            ->leftJoin('tblbufferfinancing AS bf', 'fs.bfid', '=', 'bf.bfid')
            ->leftJoin('tblcurrency as tc', function($join) {
                $join->on('fd.CurrencyID', '=', 'tc.CurrencyID')
                     ->orOn('bf.CurrencyID', '=', 'tc.CurrencyID');
            })
            ->leftJoin('tblbranch as tb', function($join) {
                $join->on('fd.BranchID', '=', 'tb.BranchID')
                     ->orOn('bf.BranchID', '=', 'tb.BranchID');
            })
            // ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            // ->join('tbladmindenom AS d', 'fs.adenomid', '=', 'd.adenomid')
            ->leftJoin('tbltransactiontype as tt', 'fd.TransType', '=', 'tt.TTID')
            ->leftJoin('tbltaggedbills as tgb', 'fs.AFSID', '=', 'tgb.AFSID')
            ->join('tblsoldtomaniladetails as stm', 'fs.STMDID', '=', 'stm.STMDID')
            // ->where('fs.Sold', '=', 0)
            // ->where('fs.Queued', '=', 1)
            ->where('fs.SoldToManila', '=', 1)
            ->whereNotNull('fs.STMDID')
            ->where('fs.FSStat', '=', 1)
            ->where('fs.FSType', 1)
            ->whereNull('tgb.AFSID')
            ->groupBy('fs.AFSID', 'tc.Currency', 'fs.Serials', 'fs.BillAmount', 'fs.CMRUsed', 'tb.BranchCode', 'stm.DateSold', 'fd.AFTDID', 'stm.STMDID', 'bf.BFID');

        $joined_queries = $branch_stocks_query->unionAll($admin_stocks_query);

        $serial_results = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('FSID as ID, FTDID as BID, Currency, BillAmount, CMRUsed, Serials, BranchCode, DateSold, STMDID, source_type, BFID')
            ->where('Serials', 'LIKE', "{$request->input('serial_search_val')}%")
            ->where('DateSold', '>', Carbon::now()->subDays(3)->toDateString())
            ->groupBy('ID', 'BID', 'Currency', 'BillAmount', 'CMRUsed', 'Serials', 'BranchCode', 'DateSold', 'STMDID', 'source_type', 'BFID')
            ->get();

        $bill_tags = DB::connection('forex')->table('tblbillstatus')
            ->get();

        $response = [
            'serial_results' => $serial_results,
            'bill_tags' => $bill_tags
        ];

        return response()->json($response);
    }

    public function save(Request $request) {
        $back_image_path = '';
        $front_image_path = '';
        $raw_date = Carbon::now('Asia/Manila');
        $bill_tags = explode(', ', trim($request->input('parsed_tags')));

        if ($request->hasFile('money-front-scanned-file')) {
            $front_image = $request->file('money-front-scanned-file');

            $resized_front = Image::make($front_image)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode($front_image->getClientOriginalExtension());

            $resized_size = strlen((string) $resized_front);

            if ($resized_size > 800 * 1024) {
                return redirect()->back()->withErrors(['money-front-scanned-file' => 'Resized image size should not exceed 800 kilobytes.'])->withInput();
            } else {
                $validator = Validator::make($request->all(), [
                    'money-front-scanned-file' => 'nullable|image|mimes:jpeg,png,jpg',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                } else {
                    $front_timestamp = now()->format('YmdHis');
                    $front_image_name = $front_timestamp . '.' . $front_image->getClientOriginalExtension();
                    $front_image_path = 'uploads/tagged_bills_images/' . $front_image_name;

                    Storage::put('public/' . $front_image_path, $resized_front);
                }
            }
            // Assuming $this->addWatermark() function is correctly defined elsewhere
            // $this->addWatermark(storage_path('app/public/' . $finalImagePath), $finalImagePath);
        } else {
            $front_image_path == null;
        }

        if ($request->hasFile('money-back-scanned-file')) {
            $back_image = $request->file('money-back-scanned-file');

            $resized_back = Image::make($back_image)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode($back_image->getClientOriginalExtension());

            $resized_size = strlen((string) $resized_back);

            if ($resized_size > 800 * 1024) {
                return redirect()->back()->withErrors(['money-front-scanned-file' => 'Resized image size should not exceed 800 kilobytes.'])->withInput();
            } else {
                $validator = Validator::make($request->all(), [
                    'money-back-scanned-file' => 'nullable|image|mimes:jpeg,png,jpg',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                } else {
                    $back_timestamp = now()->format('YmdHis');
                    $back_image_name = $back_timestamp . '.' . $front_image->getClientOriginalExtension();
                    $back_image_path = 'uploads/tagged_bills_images/' . $back_image_name;

                    Storage::put('public/' . $back_image_path, $resized_back);
                }
            }
            // Assuming $this->addWatermark() function is correctly defined elsewhere
            // $this->addWatermark(storage_path('app/public/' . $finalImagePath), $finalImagePath);
        } else {
            $back_image_path == null;
        }

        $TBTID = '';

        if ($request->input('source-type') == 1) {
            $TBTID = DB::connection('forex')->table('tbltaggedbills')
                ->insertGetId([
                    'AFTDID' => $request->input('IDs') != "null" ? $request->input('IDs') : null,
                    'BFID' => $request->input('BFID') != "null" ? $request->input('BFID') : null,
                    'AFSID' => $request->input('selected-id'),
                    'STMDID' => $request->input('STMDID'),
                    'BillAmount' => $request->input('bill-amount'),
                    'SellingRate' => $request->input('selling-rate'),
                    'ATDAmount' => floatval($request->input('bill-amount')) * floatval($request->input('selling-rate')),
                    'Serials' => trim($request->input('serial')),
                    'FrontBillImage' => $front_image_path != null ? $front_image_path : null,
                    'BackBillImage' => $back_image_path != null ? $back_image_path : null,
                    'Remarks' => $request->input('remarks') != null ? $request->input('remarks') : null,
                    'UserID' => $request->input('matched_user_id')
                ]);

        } else if ($request->input('source-type') == 2) {
            $TBTID = DB::connection('forex')->table('tbltaggedbills')
                ->insertGetId([
                    'FTDID' => $request->input('IDs') != "null" ? $request->input('IDs') : null,
                    'FSID' => $request->input('selected-id'),
                    'BFID' => $request->input('BFID') != "null" ? $request->input('BFID') : null,
                    'STMDID' => $request->input('STMDID'),
                    'BillAmount' => $request->input('bill-amount'),
                    'SellingRate' => $request->input('selling-rate'),
                    'ATDAmount' => floatval($request->input('bill-amount')) * floatval($request->input('selling-rate')),
                    'Serials' => trim($request->input('serial')),
                    'FrontBillImage' => $front_image_path != null ? $front_image_path : null,
                    'BackBillImage' => $back_image_path != null ? $back_image_path : null,
                    'Remarks' => $request->input('remarks') != null ? $request->input('remarks') : null,
                    'UserID' => $request->input('matched_user_id')
                ]);
        }

        foreach ($bill_tags as $bill_tag_ids) {
            DB::connection('forex')->table('tbltaggedbillsdetails')
                ->insert([
                    'TBTID' => $TBTID,
                    'BillStatID' => $bill_tag_ids,
                    'EntryDate' => $raw_date->toDateString(),
                    'EntryTime' => $raw_date->toTimeString(),
                ]);
        }
    }

    public function saveATDEmp(Request $request) {
        DB::connection('forex')->table('tbltaggedbills')
            ->where('tbltaggedbills.TBTID', '=', $request->input('TBTID'))
            ->update([
                'EmployeeID' => $request->input('appraiser_id')
            ]);
    }

    public function selectATDNo(Request $request) {
        $ATD_numbers = DB::connection('hrissinag')->table('tbldiscrepancy')
            ->select('tbldiscrepancy.DNO', 'tbldiscrepancy.Employee')
            ->where('tbldiscrepancy.Employee', '=', $request->input('employee_id'))
            ->orderBy('tbldiscrepancy.DNO', 'DESC')
            ->get();

        $response = [
            'ATD_numbers' => $ATD_numbers
        ];

        return response()->json($response);
    }

    public function saveATDNo(Request $request) {
        DB::connection('forex')->table('tbltaggedbills')
            ->where('tbltaggedbills.TBTID', '=', $request->input('TBTID'))
            ->update([
                'ATDNo' => $request->input('atd_no')
            ]);
    }

    public function untag(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        if ($request->input('radio-found-status') == 1) {
            DB::connection('forex')->table('tbltaggedbills')
                ->where('tbltaggedbills.TBTID', '=', $request->input('TBTID'))
                ->update([
                    'Found' => $request->input('radio-found-status'),
                    'FoundAt' => $request->input('radio-found-place'),
                ]);
        } else {
            DB::connection('forex')->table('tbltaggedbills')
                ->where('tbltaggedbills.TBTID', '=', $request->input('TBTID'))
                ->update([
                    'Found' => $request->input('radio-found-status'),
                ]);
        }
    }

    public function print(Request $request) {
        $tagged_bills = DB::connection('forex')->table('tbltaggedbills')
            ->select('tbltaggedbills.FrontBillImage', 'tbltaggedbills.BackBillImage', 'tblforexserials.FSID', 'tblforexserials.CMRUsed', 'tbltaggedbills.TBTID', 'tblcurrency.CurrAbbv', 'tblforexserials.BillAmount', 'tblforexserials.Serials', 'tblforextransactiondetails.TransactionNo', 'tblbranch.BranchCode', 'tblforextransactiondetails.TransactionDate', 'tbltaggedbills.EmployeeID', 'hris.tbllogin.FullName', DB::raw('DATE(tblsoldtomaniladetails.DateSold) as DateSold'))
            ->whereNotNull('tbltaggedbills.EmployeeID')
            ->whereNull('tbltaggedbills.ATDNo')
            ->whereBetween(DB::raw('DATE(tbltaggedbills.DateAdded)'), [$request->input('date_from'), $request->input('date_to')])
            ->join('pawnshop.tblxusers', 'tbltaggedbills.UserID', 'pawnshop.tblxusers.UserID')
            ->leftJoin('hris.tbllogin', 'tbltaggedbills.EmployeeID', 'hris.tbllogin.UserID')
            ->join('tblforextransactiondetails', 'tbltaggedbills.FTDID', 'tblforextransactiondetails.FTDID')
            ->join('tblbranch', 'tblforextransactiondetails.BranchID', 'tblbranch.BranchID')
            ->join('tblcurrency', 'tblforextransactiondetails.CurrencyID', 'tblcurrency.CurrencyID')
            ->join('tblforexserials', 'tbltaggedbills.FSID', 'tblforexserials.FSID')
            ->join('tblsoldtomaniladetails', 'tbltaggedbills.STMDID', 'tblsoldtomaniladetails.STMDID')
            ->orderBy('tbltaggedbills.TBTID', 'DESC')
            ->get();

        $TBTIDs = [];

        foreach ($tagged_bills as $index => $tagged_bills_deets) {
            $tagged_bills_details = DB::connection('forex')->table('tbltaggedbillsdetails')
                ->selectRaw('GROUP_CONCAT(tblbillstatus.BillStatus) as BillStatus')
                ->join('tblbillstatus', 'tbltaggedbillsdetails.BillStatID', 'tblbillstatus.BillStatID')
                ->where('tbltaggedbillsdetails.TBTID', '=', $tagged_bills_deets->TBTID)
                ->orderBy('tbltaggedbillsdetails.TBDID', 'DESC')
                ->get();

            $bill_stat_ids = [];

            foreach ($tagged_bills_details as $get_bill_stat_ids) {
                $bill_stat_ids[] = $get_bill_stat_ids;
            }

            $tagged_bills_deets->BillTags = $bill_stat_ids;

            $TBTIDs[] = $tagged_bills_deets;
        }

        if ($request->ajax()) {
            $html = view('bill_tagging_admin.tagged_bills_atd', ['test' => $TBTIDs])->render();
            return response()->json(['html' => $html, 'test' => $TBTIDs]);
        }

        return view('bill_tagging_admin.tagged_bills_atd')->with('test', $TBTIDs);
    }

    public function epsATD(Request $request) {
        $hr_user_id = $request->get('hr_user_id');
        $currency = $request->get('currency');
        $bill_amount = $request->get('bill_amount');
        $selling_rate = $request->get('selling_rate');
        $atd_amount = $request->get('atd_amount');
        $pawnshop_user_id = $request->get('pawnshop_user_id');
        $branch_code = $request->get('branch_code');
        $transact_date = $request->get('transact_date');
        $TBTID = $request->get('TBTID');

        $result = DB::connection('forex')->select('CALL spj_ATD_Bills(?, ?, ?, ?, ?, ?, ?, ? ,?)', [$currency, $bill_amount, $selling_rate, $hr_user_id, $atd_amount, $pawnshop_user_id, $branch_code, $transact_date, $TBTID]);

        $latest_atd = DB::connection('hrissinag')->table('tbldiscrepancydetails as dd')
            ->orderBy('dd.DDID', 'DESC')
            ->pluck('dd.DID');

        $discrepancy_no = DB::connection('hrissinag')->table('tbldiscrepancy as dc')
            ->where('dc.DID', $latest_atd)
            ->pluck('dc.DNO');

        return response()->json($discrepancy_no);
    }

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
