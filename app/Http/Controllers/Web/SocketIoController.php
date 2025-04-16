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

class SocketIoController extends Controller {
    public function save(Request $request) {
        DB::connection('forex')->table('tblforexnotification')
            ->insert([
                'Notification' => $request->get('notif'),
                'BranchID' => $request->get('branch_id')
            ]);
    }
}
