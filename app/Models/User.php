<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Helpers\ADAccountsManagement;
use Adldap\Laravel\Facades\Adldap;
use DB;
use Auth;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;

    //
    // protected $guard = ('account');
    protected $table = ('tblxusers');
    protected $primaryKey = ('UserID');
    protected $connection = 'pawnshop';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'Name',
        'Username',
        'Password',
        'Email',
        'SecurityCode',
        'BranchID',
        'LevelID',
        'WebLevelID',
        'Blocked',
        'BranchCode'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /* Get level */
    public static function getLevel(){
        $levelName = DB::connection('pawnshop')->table('tblxusers')
            ->join('accesslevel.tbllevel', 'tblxusers.accesslevelid', '=', 'tbllevel.levelid')
            ->select('tbllevel.LevelName')
            ->where('tblxusers.UserID', Auth::user()->UserID)
            ->first();

        return $levelName ? $levelName : '';

    }

    /* Get Branch */
    public static function getBranch(){
        $branchName = DB::connection('forex')->table('tblbranch')
            ->join('pawnshop.tblxbranch', 'tblbranch.BranchCode', 'pawnshop.tblxbranch.BranchCode')
            ->join('accounting.tblsegmentgroup', 'pawnshop.tblxbranch.BranchID', 'accounting.tblsegmentgroup.BranchID')
            ->join('accounting.tblcompany', 'accounting.tblsegmentgroup.CompanyID', 'accounting.tblcompany.CompanyID')
            ->join('accounting.tblsegments', 'accounting.tblsegmentgroup.SegmentID', 'accounting.tblsegments.SegmentID')
            ->select(
                'tblbranch.BranchID',
                'tblbranch.BranchCode',
                'pawnshop.tblxbranch.BranchID as pxBranchID',
                'pawnshop.tblxbranch.Address',
                'pawnshop.tblxbranch.OM',
                'accounting.tblcompany.CompanyID',
                'accounting.tblcompany.CompanyName'
            )
            ->where('accounting.tblsegments.SegmentID', 3)
            ->where('tblbranch.BranchCode', Auth::user()->BranchCode)
            ->first();

        return $branchName ?  $branchName : '';
    }

    /* verify security Code */
    public static function verifyUser(){
        $db_query = DB::connection('pawnshop')->table('tblxusers')
            ->join('pawnshop.tblxbranch', 'tblxusers.BranchCode', 'pawnshop.tblxbranch.BranchCode')
            ->join('accounting.tblsegmentgroup', 'pawnshop.tblxbranch.BranchID', 'accounting.tblsegmentgroup.BranchID')
            ->join('accounting.tblcompany', 'accounting.tblsegmentgroup.CompanyID', 'accounting.tblcompany.CompanyID')
            ->join('accounting.tblsegments', 'accounting.tblsegmentgroup.SegmentID', 'accounting.tblsegments.SegmentID')
            ->where('Blocked', 0)
            ->where('Status', 1)
            ->where('tblxusers.BranchCode', '=', Auth::user()->BranchCode);

        $db_users = $db_query->clone()
            ->select('tblxusers.Username')
            ->groupBy('tblxusers.Username')
            ->pluck('tblxusers.Username')
            ->toArray();

        $ad_users_result = Adldap::search()
            ->users()
            ->select('samaccountname')
            ->whereEnabled()
            ->whereHas('accountExpires', '>=', now()->timestamp)
            ->get()
            ->toArray();

        $ad_users = array_map(function($user) {
            return $user['samaccountname'][0];
        }, $ad_users_result);

        $return_test = '';

        $db_users_lower = array_map('strtolower', $db_users);
        $ad_users_lower = array_map('strtolower', $ad_users);

        if (!empty(array_intersect($db_users_lower, $ad_users_lower))) {
            $return_test = $db_query->clone()
                ->select('tblxusers.UserID', 'tblxusers.BranchCode', 'tblxusers.SecurityCode', 'accounting.tblcompany.CompanyID')
                ->groupBy('tblxusers.UserID', 'tblxusers.BranchCode', 'tblxusers.SecurityCode', 'accounting.tblcompany.CompanyID')
                ->get();
        } else {
            return dd('tite');
        }

        return $return_test;
    }

    /* Get Verifed User ID */
    public static function getverifedUserId($SecurityCode){
        $user = DB::connection('pawnshop')->table('tblxusers')
            ->join('tblbranch', 'tblxusers.BranchCode', 'tblbranch.BranchCode')
            ->join('pawnshop.tblxbranch', 'tblbranch.BranchCode', 'pawnshop.tblxbranch.BranchCode')
            ->join('accounting.tblsegmentgroup', 'pawnshop.tblxbranch.BranchID', 'accounting.tblsegmentgroup.BranchID')
            ->join('accounting.tblcompany', 'accounting.tblsegmentgroup.CompanyID', 'accounting.tblcompany.CompanyID')
            ->join('accounting.tblsegments', 'accounting.tblsegmentgroup.SegmentID', 'accounting.tblsegments.SegmentID')
            ->select('userid')
            ->where('PasswordExpiry', '>', now()->format('Y-m-d'))
            ->where('Blocked', '=', 0)
            ->where('SecurityCode', '=', $SecurityCode)
            ->first();

        return $user ? $user->userid : 0;
    }

    /* Get level */
    public static function getPosition(){
        $Position = DB::connection('pawnshop')->table('tblxusers')
            ->leftjoin('access.tblpositions', 'tblxusers.positionid', '=', 'tblpositions.positionid')
            ->where('tblxusers.UserID', Auth::user()->UserID)
            ->value('tblpositions.Position');

        return $Position ? $Position : "No position yet";
    }

    /* Get Municipality */
    public static function getMunicipality(){
        $municipalityName = DB::connection('pawnshop')->table('tblxbranch')
            ->join('table_province', 'tblxbranch.province_id', '=', 'table_province.province_id')
            ->select([
                'tblxbranch.BranchID',
                'tblxbranch.BranchCode',
                'tblxbranch.Address',
                'table_province.province_name'
            ])
            ->where('tblxbranch.BranchCode', Auth::user()->BranchCode)
            ->first();

        return $municipalityName ?  $municipalityName : '';
    }

    public static function findBySecurityCode($SecurityCode) {
        $user = self::select('PositionID')->where('SecurityCode', $SecurityCode)->first();

        return $user;
    }

    public static function notifications() {
        $nofitcations = DB::connection('forex')->table('tblforexnotification')
            ->where('tblforexnotification.BranchID', Auth::user()->getBranch()->BranchID)
            // ->where('tblforexnotification.BranchID', Auth::user()->getBranch()->pxBranchID)
            ->get();

        return $nofitcations;
    }

    /* Get Branch */
    public static function getBranchID(){
        return DB::connection('pawnshop')->table('tblxbranch')
            ->where(function($query) {
                $query->where('BranchCode', 'REGEXP', '^S[0-9]+');
            })
            ->where('BranchCode', '=' ,Auth::user()->BranchCode)
            ->value('BranchID');
    }

    public static function branchList() {
        $branch_list = DB::connection('forex')->table('tblbranch')
            ->join('pawnshop.tblxbranch', 'tblbranch.BranchCode', 'pawnshop.tblxbranch.BranchCode')
            ->select(
                'tblbranch.BranchID',
                'tblbranch.BranchCode',
                'pawnshop.tblxbranch.BranchID as pxBranchID',
                'pawnshop.tblxbranch.BranchCode as pxBranchCode',
                'pawnshop.tblxbranch.Address',
                'pawnshop.tblxbranch.OM',
            )
            ->get();

        return $branch_list ?  $branch_list : '';
    }

    public function hasModuleAccess(?string $userType = null)
    {
        $module = DB::connection('access')->table('tblpermissions')
                ->join('access.tblmenu', 'access.tblpermissions.MenuID', '=', 'access.tblmenu.MenuID')
                ->select('access.tblmenu.URLName')
                ->where('access.tblpermissions.PositionID', '=', $this->PositionID) // Employee's position
                ->where('access.tblpermissions.WithPermission', '=', 1) // Permission is enabled
                ->where('access.tblmenu.ApplicationID', env('SOFTWARE_ID'))
                ->when($userType, function($query, $userType){
                    return $query->where('access.tblmenu.AppMenuName', 'like', $userType . '/%');
                })
                ->orderByRaw("LENGTH(access.tblmenu.AppMenuID), access.tblmenu.AppMenuID")
                ->first();

        return $module ? $module->URLName : null;
    }
}
