<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class CustomerManagement {
    public static function all() {
        return  DB::connection('pawnshop')->table('tblxcustomer')
                ->where('tblxcustomer.NewF', '=', 1)
                ->where('tblxcustomer.Deleted', '=', 0);
    }

    // get specific customer
    public static function customerInfo($customerid) {
        return DB::connection('pawnshop')->table('tblxcustomer')
                ->where('tblxcustomer.NewF', '=', 1)
                ->where('tblxcustomer.Deleted', '=', 0)
                ->where('tblxcustomer.customerid', '=', $customerid)
                ->first();
    }

    public static function customerHasCP($customerid) {
        return DB::connection('pawnshop')->table('pawnshop.tblxcustomer')->where('CustomerID', $customerid)->value(DB::raw("IF(celno IS NULL OR celno = '', 0, 1)"));
    }

    // get type of IDs
    public static function getTypeOfCustomerIDs() {
        return DB::connection('pawnshop')->table('tblxids')->get();
    }

    // Search Customer
    public static function searchCustomer($CNumber, $FName, $MName, $LName, $CBirthday, $filter) {
        return  CustomerManagement::all()
            ->where(function ($query) use ($CNumber, $FName, $MName, $LName, $CBirthday, $filter) {
                if ($filter == 1 && $FName || $LName || $MName || $CBirthday) {
                    $query
                        ->where('tblxcustomer.FName', 'like', '%' . $FName . '%')
                        ->where('tblxcustomer.MName', 'like', '%' . $MName . '%')
                        ->where('tblxcustomer.LName', 'like', '%' . $LName . '%')
                        ->where('tblxcustomer.birthday', '=', $CBirthday);
                } elseif ($filter == 2 && $CNumber) {
                    $query->where('tblxcustomer.CustomerNo', '=', $CNumber);
                } else {
                    $query->where('tblxcustomer.CustomerNo', '=', '');
                }
            })
            ->select([
                DB::connection('pawnshop')->raw('(SELECT COUNT(*) FROM tblxcustnoid WHERE CustomerID = tblxcustomer.CustomerID) AS count'),
                'CustomerID',
                'CustomerNo',
                'Birthday',
                'FName',
                'FullName',
                'MName',
                'LName',
                'CelNo',
                'Email',
                'RiskLevel',
                'IDExpiration',
                'WithCP',
                'ScanID',
            ])
            ->where('tblxcustomer.Newf', 1)
            ->where('tblxcustomer.Deleted', 0)
            ->orderBy('tblxcustomer.FullName')
            ->limit(10)
            ->get();
    }

    // Search Customer Sanctions
    public static function searchCustomerInSanctions($CNumber, $FName, $MName, $LName, $CBirthday, $filter) {
        if ($CNumber) {
            $customer_table = self::searchCustomer($CNumber, '', '', '', '', $filter);
            $FName = $customer_table[0]->FName ?? null;
            $MName = $customer_table[0]->MName ?? null;
            $LName = $customer_table[0]->LName ?? null;
            $CBirthday = $customer_table[0]->Birthday ?? null;
        }

        return DB::table('cis.tblsanctions as s')
            ->select([
                's.FName',
                's.MName',
                's.LName',
                's.Birthday',
                's.WithSanction',
            ])
            ->where(function ($query) use ($filter, $FName, $MName, $LName, $CBirthday) {
                if ($filter == 1 && ($FName || $MName || $LName || $CBirthday)) {
                    $query->where(function ($subQuery) use ($FName, $MName, $LName, $CBirthday) {
                        $subQuery
                            ->when($FName, function ($q) use ($FName) {
                                $q->where('s.FName', 'like', '%' . $FName . '%');
                            })
                            ->when($MName, function ($q) use ($MName) {
                                $q->where('s.MName', 'like', '%' . $MName . '%');
                            })
                            ->when($LName, function ($q) use ($LName) {
                                $q->where('s.LName', 'like', '%' . $LName . '%');
                            })
                            ->when($CBirthday, function ($q) use ($CBirthday) {
                                $q->orWhere('s.Birthday', '=', $CBirthday);
                            });
                    });
                    $query->where('s.WithSanction', 1);

                } elseif ($filter == 2) {
                    $query->whereRaw('1 = 0');
                }
            })
            ->get();
    }

    // check Customer If Sinag Employee
    public static function checkCustomerIfSinagEmployee($customerid) {
        return !!DB::connection('pawnshop')->table('tblxcustomer')
            ->where('tblxcustomer.NewF', '=', 1)
            ->where('tblxcustomer.Deleted', '=', 0)
            ->where('tblxcustomer.SinagEmployee', '=', 1)
            ->where('tblxcustomer.customerid', '=', $customerid)
            ->exists();
    }
}
