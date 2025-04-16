<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;

class NotificationController extends Controller {
    protected function branch() {
        $branch_id = DB::connection('forex')->table('tblbranch as tb')
            ->selectRaw('tbx.BranchID as BranchID')
            ->join('pawnshop.tblxbranch as tbx', 'tb.BranchCode', 'tbx.BranchCode')
            ->where('tb.BranchID', Auth::user()->getBranch()->BranchID)
            ->value('BranchID');

        return [
            'branch_id' => $branch_id,
        ];
    }
    public function notifications() {
        // =======================================================
            // fetch all users Notifications
            // $query = DB::connection('forex')->table('tblforexnotification')
            //     ->leftJoin('access.tblmenu', 'tblforexnotification.menuid', '=', 'tblmenu.menuid')
            //     ->select([
            //         'tblforexnotification.FXNotifID',
            //         'tblmenu.AppMenuName',
            //         'tblforexnotification.Notification',
            //         'tblforexnotification.URLName',
            //         'tblforexnotification.Entrydate as Date',
            //         'tblforexnotification.Acknowledged',
            //     ])
                
            //     ->where('tblforexnotification.BranchID', Auth::user()->getBranch()->BranchID)
            //     ->where('tblforexnotification.PositionID', Auth::user()->PositionID);
            //     // ->get();

            // $whole_count = $query->clone()
            //     ->where('tblforexnotification.Acknowledged', 0)
            //     ->get();

            // $count = $whole_count->count();

            // $notifications = $query->get();
        // =======================================================
        
        $branch_id = $this->branch()['branch_id'];

        $query = DB::connection('forex')->table('tblfxnotifcontrol as fxc')
            ->join('tblfxnotifdetails as fxd', 'fxc.FXCID', 'fxd.FXCID')
            ->leftJoin('access.tblmenu as tm', 'fxc.MenuID', 'tm.MenuID')
            ->where('fxc.BranchID', $branch_id)
            ->where('fxd.UserID', Auth::user()->UserID)
            ->where('fxd.PositionID', Auth::user()->PositionID);

        $notifications = $query->clone()
            ->selectRaw('fxd.FXDID, tm.AppMenuName, fxc.Notification, fxd.URLName, fxc.Entrydate as Date, fxd.Acknowledged, fxc.BranchID')
            ->groupBy('fxd.FXDID', 'tm.AppMenuName', 'fxc.Notification', 'fxd.URLName', 'Date', 'fxd.Acknowledged', 'fxc.BranchID')
            ->get();

        $new_notifs = $query->clone()->where('fxd.Acknowledged', 0)
            ->selectRaw('fxd.FXDID')
            ->groupBy('fxd.FXDID')
            ->get();

        $count = $new_notifs->count();

        if ($notifications->isNotEmpty()) {
            return response()->json(['success' => 1, 'data' => $notifications, 'count' => $count]);
        } else {
            return response()->json(['success' => 0]);
        }
    }

    public function show() {
        $branch_id = $this->branch()['branch_id'];

        $result['notifs'] = DB::connection('forex')->table('tblfxnotifcontrol as fxc')
            ->selectRaw('fxd.FXDID, tm.AppMenuName, fxc.Notification, fxd.URLName, fxc.Entrydate as Date, fxd.Acknowledged')
            ->join('tblfxnotifdetails as fxd', 'fxc.FXCID', 'fxd.FXCID')
            ->leftJoin('access.tblmenu as tm', 'fxc.MenuID', 'tm.MenuID')
            ->where('fxc.BranchID', $branch_id)
            ->where('fxd.UserID', Auth::user()->UserID)
            ->groupBy('fxd.FXDID', 'tm.AppMenuName', 'fxc.Notification', 'fxd.URLName', 'Date', 'fxd.Acknowledged')
            ->orderBy('fxd.FXDID', 'DESC')
            ->paginate(30);

        return view('blades.notifications', compact('result'));
    }

    public function acknowledge(Request $request) {
        $branch_id = $this->branch()['branch_id'];

        DB::connection('forex')->table('tblfxnotifcontrol as fxc')
            ->join('tblfxnotifdetails as fxd', 'fxc.FXCID', 'fxc.FXCID')
            ->leftJoin('access.tblmenu as tm', 'fxc.MenuID', 'tm.MenuID')
            ->where('fxc.BranchID', $branch_id)
            ->where('fxd.FXDID', $request->get('FXDID'))
            ->update([
                'Acknowledged' => 1
            ]);

        // DB::connection('forex')->table('tblfxnotifcontrol as fxc')
        //     ->where('fxc.FXCID', $request->get('FXCID'))
        //     ->update([
        //         'Acknowledged' => 1
        //     ]);

        return view('blades.notifications', compact('result'));
    }
}
