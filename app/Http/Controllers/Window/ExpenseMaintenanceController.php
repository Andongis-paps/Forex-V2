<?php

namespace App\Http\Controllers\Window;
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
use Dotenv\Validator as DotenvValidator;
use Session;
//for requesting a value
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;

class ExpenseMaintenanceController extends Controller
{
    public function expenseMaintenance(Request $request) {
        $order = session('session', 'ASC');

        $result['expenses'] = DB::connection('forex')->table('tblexpenses')
            ->orderBy('tblexpenses.EID', $order)
            ->paginate(10);

        return view('window.expense_mainte.expense_maintenance', compact('order' , 'result'));
    }

    public function addNewExpense(Request $request) {
        $expense_name = $request->input('expense_name');

        $validator = Validator::make($request->all(), [
            'expense_name' => 'required|regex: /^[a-zA-z]\s*/'
        ]);

        if($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            DB::connection('forex')->table('tblexpenses')
                ->insert([
                    'ExpenseName' => $expense_name
                ]);
        }

        $message = "New expense added!";
        return redirect()->back()->with('message' , $message);
    }

    public function editExpense(Request $request) {
        $expense_details = DB::connection('forex')->table('tblexpenses')
            ->where('tblexpenses.EID', '=' , $request->ExpenseID)
            ->get();

        return view('window.expense_mainte.update_expense_mainte_modal')->with('expense_details', $expense_details);
    }

    public function updateExpense(Request $request) {
        $validator = Validator::make($request->all(), [
            'expense_name' => 'required'
        ]);

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        } else {
            DB::connection('forex')->table('tblexpenses')
                ->where('tblexpenses.EID', '=' , $request->expense_id)
                ->update([
                    'ExpenseName' => $request->expense_name,
                ]);
        }

        $message = "Expense details updated!";
        return redirect()->back()->with('message', $message);
    }

    public function searchExpense(Request $request) {
        $keyword = $request->get('search_word');

        $expenses = DB::connection('forex')->table('tblexpenses')
            ->where('tblexpenses.ExpenseName' , 'LIKE' , "%{$keyword}%")
            ->get();

        return response()->json($expenses);
    }
}
