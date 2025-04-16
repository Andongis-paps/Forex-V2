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

class ConfigurationController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:CONFIGURATION,VIEW')->only(['show']);
        $this->middleware('check.access.permission:CONFIGURATION,ADD')->only(['']);
        $this->middleware('check.access.permission:CONFIGURATION,EDIT')->only(['update']);
        $this->middleware('check.access.permission:CONFIGURATION,DELETE')->only(['']);
    }

    public function show(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $session = DB::connection('laravelsysconfigs')->table('tblxsessions')
            ->where('software_id', config('app.software_id'))
            ->first();

        if ($session) {
            $lifetimeMinutes = $session->lifetime;
            $hours = floor($lifetimeMinutes / 60); // Calculate hours
            $remainingMinutes = $lifetimeMinutes % 60; // Calculate remaining minutes
            $seconds = $remainingMinutes * 60; // Calculate seconds

            $sessions = [
                'hours' => $hours,
                'seconds' => $remainingMinutes,
                'expire_on_close' => $session->expire_on_close,
                'attemps' => $session->attemps,
                'waiting_time' => $session->waiting_time,
            ];
        }

        return view('window.configuration_mainte', compact('sessions', 'menu_id'));
    }

    public function update(Request $request) {
        $time_string = $request->lifetime;
        list($hours, $minutes) = explode(':', $time_string);
        $total_minutes = ($hours * 60) + $minutes;

        $data['update'] = DB::connection('laravelsysconfigs')->table('tblxsessions')
            ->where('software_id', config('app.software_id'))
            ->update([
                'lifetime' => $total_minutes,
                'expire_on_close' => $request->expire_on_close,
                'attemps' => $request->attemps,
                'waiting_time' => $request->waiting_time
            ]);

        session(['attemps_limit' => $request->attemps]);

        return redirect()->back()->with(['success' => 1, 'title' => 'Done', 'message' => 'Successfully Updated!']);

    }
}
