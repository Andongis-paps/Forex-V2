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
use Illuminate\Database\Query\Builder;

class UtilitiesController extends Controller
{
    public function userLevels() {
        $result['user_levels'] = DB::connection('pawnshop')
            ->table('userlevels')
            ->get();

        return view('utilities.user_levels')->with('result' , $result);
    }

    public function branchBuffer() {
        $order = session('order', 'ASC');

        if(isset($_GET['toggle'])) {
            $order = ($order === 'ASC') ? 'DESC' : 'ASC';
            session(['order' => $order]);
        }

        $result['branch_buffer'] = DB::connection('forex')->table('tblbufferbranch')
            ->leftJoin('tblbranch' , 'tblbufferbranch.BranchID', '=' , 'tblbranch.BranchID')
            ->select(
                'tblbranch.BranchCode',
                'tblbufferbranch.BranchID'
            )
            ->orderBy('tblbufferbranch.BranchID' , $order)
            ->paginate(10);

        $sortedBranch = [];

        foreach($result['branch_buffer'] as $branches) {
            $sortedBranch[] = $branches->BranchID;
        }

        if($order === 'DESC') {
            $sortedBranch = array_reverse($sortedBranch);
        }

        return view('utilities.branch_buffer', compact('result' , 'sortedBranch', 'order'));
    }
}
