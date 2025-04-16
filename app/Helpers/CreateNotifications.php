<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon;
use App\Models\User;

class CreateNotifications {
    public static function getAdminMenuURLName($menuid) {
        $URLName = DB::connection('access')->table('tblmenu')->where('MenuID', $menuid)->value('URLName');
        return strtolower($URLName);
    }

    //-------------- Branch Notifications ------------//
    public static function CreateBranchNotifications($branches = [], $menuid, $notification, $url) {
        $forex_branch_code = [];
        $BranchIDs = [];

        if(empty($branches)) {
            $BranchIDs = self::getAllBranchID();
            $forex_branch_code = self::getAllBranchCode();
        } else {
            $forex_branch_code = DB::connection('forex')->table('tblbranch as tb')
                ->when(is_array($branches), function ($query) use ($branches) {
                    return $query->whereIn('tb.BranchID', $branches);
                }, function ($query) use ($branches) {
                    return $query->where('tb.BranchID', $branches);
                })->orderBy('tb.BranchCode', 'ASC')
                ->pluck('BranchCode')
                ->toArray();

            $pawnshop_branch_id = DB::connection('pawnshop')->table('tblxbranch as tbx')
                ->when(is_array($forex_branch_code), function ($query) use ($forex_branch_code) {
                    return $query->whereIn('tbx.BranchCode', $forex_branch_code);
                }, function ($query) use ($forex_branch_code) {
                    return $query->where('tbx.BranchCode', $forex_branch_code);
                })->orderBy('tbx.BranchID', 'ASC')
                ->pluck('BranchID')
                ->toArray();

            // $BranchIDs = $branches;
            $BranchIDs = $pawnshop_branch_id;
        }

        $user_info = DB::connection('pawnshop')->table('tblxusers as txu')
            ->selectRaw('tbx.BranchID, txu.UserID, tp.PositionID, tp.DepartmentID, txu.OM')
            ->join('tblxbranch as tbx', 'txu.BranchCode', 'tbx.BranchCode')
            ->join('access.tblpositions as tp', 'txu.PositionID', 'tp.PositionID')
            ->when(is_array($forex_branch_code), function ($query) use ($forex_branch_code) {
                return $query->whereIn('txu.BranchCode', $forex_branch_code);
            }, function ($query) use ($forex_branch_code) {
                return $query->where('txu.BranchCode', $forex_branch_code);
            })->where('txu.Status', 1)
            ->where('txu.Blocked', 0)
            ->groupBy('tbx.BranchID', 'txu.UserID', 'tp.PositionID', 'tp.DepartmentID', 'txu.OM')
            ->get();

        foreach ($BranchIDs as $value) {
            $FXCID = DB::connection('forex')->table('tblfxnotifcontrol')->insertGetID([
                    'ApplicationID' => config('app.software_id'),
                    'MenuID' => $menuid, 
                    'BranchID' => $value, 
                    'Notification' => $notification,   
                ]);

            foreach ($user_info as $values) {
                if ($value == $values->BranchID) {
                    DB::connection('forex')->table('tblfxnotifdetails')->insert([
                        'FXCID' => $FXCID,
                        'URLName' => self::getMenuURLName($menuid, $url),
                        'UserID' => $values->UserID,
                        'PositionID' => $values->PositionID,
                        'DepartmentID' => $values->DepartmentID,
                    ]);

                }
            }
        }
    }

    //-------------- Admin Notifications ------------//
    public static function CreateAdminNotifications($positions = [], $menuid, $notification) {
        $admin_positions = self::getAllBranch();

        foreach ($admin_positions as $positionid) {
            DB::connection('forex')->table('tblforexnotification')->insert([
                'ApplicationID' => config('app.software_id'),
                'MenuID'        => $menuid,
                'Notification'  => $notification,
                'BranchID'      => 1,
                'PositionID'    => $positionid,
                'DepartmentID'  => 3,
            ]);
        }
    }

    //-------------- Super Admin Notifications ------------//
    public static function CreateSuperAdminNotifications($positions = [], $menuid, $notification) {
        $admin_positions = self::getPositions(13);

        foreach ($admin_positions as $pid) {
            DB::connection('forex')->table('tblforexnotification')->insert([
                'ApplicationID' => config('app.software_id'),
                'MenuID'        => $menuid,
                'Notification'  => $notification,
                'BranchID'      => 1,
                'PositionID'    => $pid,
                'DepartmentID'  => 3,
            ]);
        }
    }

    //-------------- OM Notifications ------------//
    public static function CreateOMNotifications($positions = [], $menuid, $notification) {
        DB::connection('forex')->table('tblforexnotification')->insert([
            'ApplicationID' => config('app.software_id'),
            'MenuID'        => $menuid,
            'Notification'  => $notification,
            'BranchID'      => 1,
            'PositionID'    => 1,
            'DepartmentID'  => 11, // Ensure $positionid is defined earlier
        ]);
    }

    //-------------- get BranchGroupID by BranchID ------------//
    public static function getBranchGroupIDbyBranchID($BranchID) {
        return DB::connection('pawnshop')->table('tblgrate')->where('systembranchid', $BranchID)->latest('RateID')->value('GroupID');
    }

    //-------------- get Branch ------------//
    public static function getAllBranchCode() {
        return DB::connection('pawnshop')->table('tblxbranch')
            ->where(function($query) {
                $query->where('tblxbranch.BranchCode', 'REGEXP', '^S[0-9]+');
            })
            ->where('IsActive', 1)
            ->orderByRaw("LENGTH(tblxbranch.BranchCode) , tblxbranch.BranchCode")
            ->pluck('BranchCode')->toArray();
    }

    public static function getAllBranchID() {
        return DB::connection('pawnshop')->table('tblxbranch')
            ->where(function($query) {
                $query->where('tblxbranch.BranchCode', 'REGEXP', '^S[0-9]+');
            })
            ->where('IsActive', 1)
            ->orderByRaw("LENGTH(tblxbranch.BranchCode) , tblxbranch.BranchCode")
            ->pluck('BranchID')->toArray();
    }

    //-------------- get Positions ------------//
    public static function getPositions($pid) {
        if(empty($pid)) {
            return DB::connection('access')->table('tblpositions')->pluck('PositionID')->toArray();
        }  else {
            return DB::connection('access')->table('tblpositions')->where('PositionID', $pid)->pluck('PositionID')->toArray();
        }
    }

    public static function getMenuURLName($menuid, $url = '') {
        if($url) {
            $URLName = $url;
        }else{
            $URLName = DB::connection('access')->table('tblmenu')->where('MenuID', $menuid)->value('URLName');
        }
       
        return strtolower($URLName);
    }
}
