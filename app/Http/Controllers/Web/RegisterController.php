<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Validator;
use App;
use Lang;
use App\Admin;

use DB;
//for password encryption or hash protected
use Hash;
//use App\Administrator;

//for authenitcate login data
use Auth;
use Session;
//for requesting a value
use Illuminate\Http\Request;

class RegisterController extends Controller {
    public function register() {
        return view('user.register');
    }

    public function storeAccount(Request $request) {
        $name = $request->input('name');
        $username = $request->input('username');
        $password = $request->input('password');
        $password_confirmation = $request->input('password_confirmation');

        // Section that makes the password hashed/encrpyted***
        // $name = $request->input('name');
        // $username = $request->input('username');
        // $password = Hash::make($request->input('password'));
        // $password_confirmation = $request->input('password_confirmation');

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required|regex:/^[\pL\s\-]+$/u|unique:tblusers',
            'password' => 'required|min:8|confirmed'
        ]);

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        } else {
            DB::connection('forex')->table('tblusers')
            ->insert([
                'name' => $name,
                'username' => $username,
                'password' => $password,
            ]);
        }

        return view('user.login');
    }
}
