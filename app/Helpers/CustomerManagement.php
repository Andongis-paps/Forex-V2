<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB; 
use App\Helpers\Errors\ErrorHandler;
use Illuminate\Support\Facades\Validator;

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
        $customers = CustomerManagement::all()
            ->where(function ($query) use ($CNumber, $FName, $MName, $LName, $CBirthday, $filter) {
                if ($filter == 1 && $FName || $LName || $MName || $CBirthday) {
                    $query
                        ->where('FName', 'like', '%' . $FName . '%')
                        ->where('MName', 'like', '%' . $MName . '%')
                        ->where('LName', 'like', '%' . $LName . '%')
                        ->where('birthday', '=', $CBirthday);
                } elseif ($filter == 2 && $CNumber) {
                    $query->where('CustomerNo', '=', $CNumber);
                } else {
                    $query->where('CustomerNo', '=', '');
                }
            })
            ->select([
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
            ->where('Newf', 1)
            ->where('Deleted', 0)
            ->orderBy('FullName')
            ->limit(10)
            ->get();
        
        foreach ($customers as $customer) {
            $evaluateCustomer   = self::evaluateCustomerStatus($customer->CustomerID, null);
            $customer->Status   = $evaluateCustomer['status'];
            $customer->Reason   = $evaluateCustomer['reason'];
            $customer->Photo    = self::getCustomerPhoto($customer->CustomerID);
            // $customer->Birthday = ConfigurationManagement::customDateFormat($customer->Birthday);
            $customer->Birthday =  Carbon::parse($customer->Birthday)->format('Y-m-d');
        }

        return $customers;
    }

    // Search Customer Sanctions
    public static function searchCustomerInSanctions($CNumber, $FName, $MName, $LName, $CBirthday, $filter) {
        if ($CNumber) {
            $customer_table = self::searchCustomer($CNumber, '', '', '', '', $filter);
            $FName = $customer_table[0]->Fname ?? null;
            $MName = $customer_table[0]->Mname ?? null;
            $LName = $customer_table[0]->Lname ?? null;
            $CBirthday = $customer_table[0]->Birthdate ?? null;
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
            ->where('tblxcustomer.NewF', 1)
            ->where('tblxcustomer.Deleted', 0)
            ->where('tblxcustomer.SinagEmployee', 1)
            ->where('tblxcustomer.customerid', $customerid)
            ->exists();
    }

    public static function evaluateCustomerStatus($cid = null, $branchcode = null) {
        if ($cid) {
            $validations = DB::select("CALL pawnshop.cms_validate_customer(?, ?)", [$cid, $branchcode]);
   
            $reasons = [];
   
            foreach ($validations as $validation) {
                if (isset($validation->msg) && strtolower(trim($validation->msg)) !== 'ok') {
                    $reasons[] = trim($validation->msg);
                } 
            }
   
            if (!empty($reasons)) {
                $reasonList = '<ul  class="list-group">';

                foreach ($reasons as $msg) {
                    $reasonList .= '<li class="list-group-item">' . htmlspecialchars($msg) . '</li>';
                }

                $reasonList .= '</ul>';
   
                return [
                    'status' => false,
                    'reason' => $reasonList
                ];
            }
        }
   
        return [
            'status' => true,
            'reason' => ''
        ];
    }

    public static function getCustomerPhoto($customerid){
        $basePhotoUrl = config('app.cms_photo_location_ip');
        $customer = self::customerInfo($customerid);
        $photoPath = '';

        if (!empty($customer->ScanID)) {
            $photoPath = $customer->ScanID;
        } elseif (!empty($customer->IDPicture)) {
            $photoPath = $customer->IDPicture;
        }
    
        // Construct the full customer photo URL or use a default image
        if (!empty($photoPath)) {
            return $basePhotoUrl . $photoPath;
        } else {
            return asset('assets/img/default-customer-img.jpg');
        }
    }

    // validate Requests
    public static function validateRequest($request){
        $isMethod = $request->isMethod('POST');

        $rules = [];
        $messages = [];
        $filter = $request->input('filter');

        /* ================================
        |  Searching Customer Validations  |
        ================================= */

        // Birthday
        if ($filter == 1) {
            $rules['Birthdate']                 = 'required|date';
            $messages['Birthdate.required']     = 'Birthdate field is required.';
            $messages['Birthdate.date']         = 'Birthdate must be a valid date.';

            $rules['Fname'] = 'nullable|string';
            $rules['Mname'] = 'nullable|string';
            $rules['Lname'] = 'nullable|string';
        }
        // CustomerID Validation
        if ($filter == 2) {
            $rules['Cnumber']              = 'required|integer|exists:pawnshop.tblxcustomer,customerid';
            $messages['Cnumber.required']  = 'Customer is required.';
            $messages['Cnumber.exists']    = 'Customer not found.';
        }


        $validator = Validator::make($request->all(), $rules, $messages);

        // Additional validation for name fields (at least two must be filled)
        if ($filter == 1) {
            $nameFields = array_filter([
                $request->Fname,
                $request->Mname,
                $request->Lname
            ]);

            if (count($nameFields) < 2) {
                $validator->after(function ($validator) {
                    $validator->errors()->add('name_fields', 'At least two of the name fields are required.');
                });
            }
        }

        if ($validator->fails()) return response()->json(['errors' => 1, 'title' => '', 'html' => ErrorHandler::formatErrors($validator->errors())]);

        return null;
    }
}
