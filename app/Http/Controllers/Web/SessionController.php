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
use Illuminate\Support\Facades\Artisan;
use App\Helpers\CustomerManagement;

class SessionController extends Controller {
    public function updateTimeToggleSession(Request $request) {
        $time_togg_val = $request->get('time_toggle_session');

        if (intval($time_togg_val) == 1) {
            session(['time_toggle_status' =>  1]);
        } else if (intval($time_togg_val) == 0) {
            session(['time_toggle_status' =>  0]);
        }

        $response = [
            'message' => 'huli ka balbon',
            'get_time_toggle_stat' => $time_togg_val
        ];

        return response()->json($response);
    }

    public function userInfo(Request $request) {
        return response()->json(['security_codes' => User::verifyUser()]);
    }

    public function searchCustomer(Request $request) {
        $search_filter = $request->input('filter');
        $customer_details = $request->input('customer_details');
        $customer_l_name = $request->input('Lname');
        $customer_f_name = $request->input('Fname');
        $customer_m_name = $request->input('Mname');
        $customer_number = $request->input('Cnumber');
        $birth_date = $request->input('Birthdate');

        $validations = CustomerManagement::validateRequest($request);
 
        if ($validations) return $validations;
        
        $data['sanctions'] = CustomerManagement::searchCustomerInSanctions($customer_number, $customer_f_name, $customer_m_name, $customer_l_name, $birth_date, $search_filter);

        $data['customers'] = CustomerManagement::searchCustomer($customer_number, $customer_f_name, $customer_m_name, $customer_l_name, $birth_date, $search_filter);

        return response()->json($data);
    }

    public function testAutoRunSchedulers(Request $request) {
        // You can run the command directly
        Artisan::call('schedule:work');

        // Optionally get the output if needed
        $output = Artisan::output();

    }
}
