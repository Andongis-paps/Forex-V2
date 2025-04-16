<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $connection = 'access';

    protected $table = ('tblmenu');

    protected $primaryKey = ('MenuID');

    public $timestamps = false;

    public static function findByAppMenuName($AppMenuName) {
        $menu = self::where('ApplicationID', config('app.software_id'))
                        ->where('AppMenuName', $AppMenuName)
                        ->first();

        return $menu;
    }
}
