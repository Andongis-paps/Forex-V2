<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Validator;
use App;
use Lang;
use App\Admin;
use App\Models\User;
use DB;
//for password encryption or hash protected
use Hash;
//use App\Administrator;

//for authenitcate login data
use Auth;
use Session;
//for requesting a value
use Illuminate\Http\Request;

class UserController extends Controller {                  
    public function loginProcess(Request $request) {
        $result = array();
        $message = "Mali man, bobo mo naman";
        $customerInfo = array("username" => $request->username, "password" => $request->password);

        if(auth()->guard('account')->attempt($customerInfo)) {
			return redirect()->intended('/')->with('result', $result);
        } else {
            return redirect()->back()->with('loginError' , $message);
        }
    }


}
