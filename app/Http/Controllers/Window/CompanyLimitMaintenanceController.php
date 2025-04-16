<?php

namespace App\Http\Controllers\Window;

use App\Http\Controllers\Controller;
use Validator;
use App;
use Lang;
use App\Admin;
use DB;
use Hash;
use Auth;
use Session;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;

class CompanyLimitMaintenanceController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:COMPANY LIMIT MAINTENANCE,VIEW')->only(['show']);
        $this->middleware('check.access.permission:COMPANY LIMIT MAINTENANCE,ADD')->only(['add']);
        $this->middleware('check.access.permission:COMPANY LIMIT MAINTENANCE,EDIT')->only(['edit', 'update']);
        $this->middleware('check.access.permission:COMPANY LIMIT MAINTENANCE,DELETE')->only(['']);
    }

    public function show(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['company_limit'] = DB::connection('forex')->table('tblcompanylimitcontrol AS cld')
            ->selectRaw('cld.CLDID, tc.CompanyName, cld.CompanyID, cld.AnnualLimit, cld.Status, cld.SeriesO, cld.EntryDateTime, tbx.Name')
            // ->join('tblcompanylimitdetails as cl', 'cld.CompanyID', 'cl.CompanyID')
            ->join('accounting.tblcompany as tc', 'cld.CompanyID', 'tc.CompanyID')
            ->join('pawnshop.tblxusers as tbx', 'cld.UserID', 'tbx.UserID')
            ->groupBy('cld.CLDID', 'tc.CompanyName', 'cld.CompanyID', 'cld.AnnualLimit', 'cld.Status', 'cld.SeriesO', 'cld.EntryDateTime', 'tbx.Name')
            ->paginate(15);

        $amount = [];
        $percentage = [];

        foreach ($result['company_limit'] as $value) {
            $data = DB::connection('forex')->table('tblcompanylimitdetails as cl')
                ->where('cl.CLDID', '=', $value->CLDID)
                ->selectRaw('cl.Percentage, cl.Amount')
                ->get();

            $get_amount = [];
            $get_percentage = [];

            foreach ($data as $values) {
                $get_amount[] = $values->Amount;
                $get_percentage[] = $values->Percentage;
            }

            $value->amount = $get_amount;
            $value->percentage = $get_percentage;

            $amount[] = $value;
            $percentage[] = $value;
        }

        return view('window.company_limit_mainte.company_limit', compact('result', 'menu_id'));
    }

    public function add(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['month_config'] = DB::connection('forex')->table('tblpercentageconfig')
            ->get();

        $result['company'] = DB::connection('forex')->table('tblbranch')
            ->selectRaw('accounting.tblcompany.CompanyID, accounting.tblcompany.CompanyName')
            ->join('pawnshop.tblxbranch', 'tblbranch.BranchCode', 'pawnshop.tblxbranch.BranchCode')
            ->join('accounting.tblsegmentgroup', 'pawnshop.tblxbranch.BranchID', 'accounting.tblsegmentgroup.BranchID')
            ->join('accounting.tblcompany', 'accounting.tblsegmentgroup.CompanyID', 'accounting.tblcompany.CompanyID')
            ->join('accounting.tblsegments', 'accounting.tblsegmentgroup.SegmentID', 'accounting.tblsegments.SegmentID')
            ->where('accounting.tblsegments.SegmentID', '=', 3)
            ->whereIn('accounting.tblcompany.CompanyID', [3, 4, 5, 6, 15])
            ->groupBy('accounting.tblcompany.CompanyID', 'accounting.tblcompany.CompanyName')
            ->orderBy('accounting.tblcompany.CompanyName', 'ASC')
            ->get();

        return view('window.company_limit_mainte.add_company_limit', compact('result', 'menu_id'));
    }

    public function save(Request $request) {
        $exploded_month = explode(",", trim($request->input('month')));
        $month = array_map('trim', $exploded_month);

        $exploded_amount = explode(", ", trim($request->input('amount')));
        $amount = array_map('trim', $exploded_amount);

        $exploded_percentage = explode(",", trim($request->input('percentage')));
        $percentage = array_map('trim', $exploded_percentage);

        $CLDID = DB::connection('forex')->table('tblcompanylimitcontrol')
            ->insertGetId([
                'CompanyID' => $request->input('company'),
                'AnnualLimit' => $request->input('annual-amount'),
                'SeriesO' => $request->input('series-set-o'),
                'UserID' => $request->get('matched_user_id'),
            ]);

        foreach ($percentage as $key => $value) {
            DB::connection('forex')->table('tblcompanylimitdetails')
                ->insert([
                    'CLDID' => $CLDID,
                    'Month' => $month[$key],
                    'Percentage' => $value,
                    'Amount' => preg_replace('/[^\d.]/', '', $amount[$key])
                    // 'Amount' => $amount[$key]
                ]);
        }
    }

    public function edit(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $CLDID = $request->CLDID;

        $result['month_config'] = DB::connection('forex')->table('tblpercentageconfig')
            ->get();

        $result['annual_limit'] = DB::connection('forex')->table('tblcompanylimitcontrol AS cld')
            ->where('cld.CLDID', $request->CLDID)
            ->pluck('cld.AnnualLimit');

        $result['series_o'] = DB::connection('forex')->table('tblcompanylimitcontrol AS cld')
            ->where('cld.CLDID', $request->CLDID)
            ->pluck('cld.SeriesO');

        $result['current_comp'] = DB::connection('forex')->table('tblcompanylimitcontrol AS cld')
            ->where('cld.CLDID', $request->CLDID)
            ->pluck('cld.CompanyID')
            ->toArray();

        $result['company'] = DB::connection('forex')->table('tblbranch')
            ->selectRaw('accounting.tblcompany.CompanyID, accounting.tblcompany.CompanyName')
            ->join('pawnshop.tblxbranch', 'tblbranch.BranchCode', 'pawnshop.tblxbranch.BranchCode')
            ->join('accounting.tblsegmentgroup', 'pawnshop.tblxbranch.BranchID', 'accounting.tblsegmentgroup.BranchID')
            ->join('accounting.tblcompany', 'accounting.tblsegmentgroup.CompanyID', 'accounting.tblcompany.CompanyID')
            ->join('accounting.tblsegments', 'accounting.tblsegmentgroup.SegmentID', 'accounting.tblsegments.SegmentID')
            ->where('accounting.tblsegments.SegmentID', '=', 3)
            ->whereIn('accounting.tblcompany.CompanyID', [3, 4, 5, 6, 15])
            ->groupBy('accounting.tblcompany.CompanyID', 'accounting.tblcompany.CompanyName')
            ->orderBy('accounting.tblcompany.CompanyName', 'ASC')
            ->get();

        $result['company_limit'] = DB::connection('forex')->table('tblcompanylimitcontrol AS cld')
            ->selectRaw('cld.CLDID, tc.CompanyName, cld.CompanyID, cld.AnnualLimit, cld.Status, cld.EntryDateTime, tbx.Name')
            // ->join('tblcompanylimitdetails as cl', 'cld.CompanyID', 'cl.CompanyID')
            ->join('accounting.tblcompany as tc', 'cld.CompanyID', 'tc.CompanyID')
            ->join('pawnshop.tblxusers as tbx', 'cld.UserID', 'tbx.UserID')
            ->where('cld.CLDID', $request->CLDID)
            ->groupBy('cld.CLDID', 'tc.CompanyName', 'cld.CompanyID', 'cld.AnnualLimit', 'cld.Status', 'cld.EntryDateTime', 'tbx.Name')
            ->get();

        $CLIDs = [];
        $amount = [];
        $percentage = [];

        foreach ($result['company_limit'] as $value) {
            $data = DB::connection('forex')->table('tblcompanylimitdetails as cl')
                ->where('cl.CLDID', '=', $value->CLDID)
                ->selectRaw('cl.Percentage, cl.Amount, cl.CLID')
                ->get();

            $CLID = [];
            $get_amount = [];
            $get_percentage = [];

            foreach ($data as $values) {
                $CLID[] = $values->CLID;
                $get_amount[] = $values->Amount;
                $get_percentage[] = $values->Percentage;
            }

            $value->CLIDs = $CLID;
            $value->amount = $get_amount;
            $value->percentage = $get_percentage;

            $CLIDs[] = $value;
            $amount[] = $value;
            $percentage[] = $value;
        }

        return view('window.company_limit_mainte.edit_company_limit', compact('result', 'menu_id', 'CLDID'));
    }

    public function update(Request $request) {
        $exploded_ids = explode(",", trim($request->input('CLIDs')));
        $IDs = array_map('trim', $exploded_ids);

        $exploded_amount = explode(", ", trim($request->input('amount')));
        $amount = array_map('trim', $exploded_amount);

        $exploded_percentage = explode(",", trim($request->input('percentage')));
        $percentage = array_map('trim', $exploded_percentage);

        DB::connection('forex')->table('tblcompanylimitcontrol')
            ->where('tblcompanylimitcontrol.CLDID', $request->get('CLDID'))
            ->update([
                'CompanyID' => $request->input('company'),
                'AnnualLimit' => $request->input('annual-amount'),
                'SeriesO' => $request->input('series-set-o'),
                'UserID' => $request->get('matched_user_id'),
            ]);

        foreach ($IDs as $key => $values) {
            DB::connection('forex')->table('tblcompanylimitdetails')
                ->where('CLID', $values)
                ->update([
                    'Percentage' => $percentage[$key],
                    'Amount' => preg_replace('/[^\d.]/', '', $amount[$key])
                    // 'Amount' => $amount[$key]
                ]);
        }
    }
}
