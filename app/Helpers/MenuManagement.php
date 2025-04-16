<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MenuManagement {
    public static function getAdminMenus() {
        // Fetch menus where AppMenuName starts with 'ADMIN/' for SPC Portal Application
        $menus = DB::connection('access')
                    ->table('tblmenu as m')
                    ->leftJoin('tblpermissions as p', function($join) {
                        $join->on('m.MenuID', '=', 'p.MenuID')
                            ->where('p.ApplicationID', config('app.software_id'))
                            ->where('p.PositionID', Auth::user()->PositionID)
                            ->where('p.WithPermission', 1);
                    })
                    ->where('m.ApplicationID', config('app.software_id'))
                    // ->where('m.AppMenuName', 'like', 'ADMIN/%') // Filter by AppMenuName starting with 'ADMIN/'
                    ->where('p.PositionID', Auth::user()->PositionID)
                    ->where('p.WithPermission', 1)
                    ->select('m.MenuID', 'm.AppMenuName', 'm.AppMenuID', 'm.URLName')
                    ->orderBy('m.AppMenuID') // Order by AppMenuID (like SPC01, SPC02)
                    ->get();

        return $menus;
    }

    public static function getAdminMenuItems() {
        // Fetch menus where AppMenuName starts with 'ADMIN/' for SPC Portal Application
        $menus = DB::connection('access')
                    ->table('tblmenu as m')
                    ->leftJoin('tblpermissions as p', function($join) {
                        $join->on('m.MenuID', '=', 'p.MenuID')
                            ->where('p.ApplicationID', config('app.software_id'))
                            ->where('p.PositionID', Auth::user()->PositionID)
                            ->where('p.WithPermission', 1);
                    })
                    ->where('m.ApplicationID', config('app.software_id'))
                    // ->where('m.AppMenuName', 'like', 'ADMIN/%') // Filter by AppMenuName starting with 'ADMIN/'
                    ->where('p.PositionID', Auth::user()->PositionID)
                    ->where('p.WithPermission', 1)
                    ->select('m.MenuID', 'm.AppMenuName', 'm.AppMenuID', 'm.URLName')
                    ->orderBy('m.AppMenuID') // Order by AppMenuID (like SPC01, SPC02)
                    ->get();

        return self::buildAdminMenuTree($menus);
    }

    // Function to build admin menu tree using URLName as hierarchical structure
    private static function buildAdminMenuTree($menus)
    {
        $menuTree = [];

        foreach ($menus as $menu) {
            // Remove 'ADMIN/' prefix from URLName
            $cleanUrlName = str_replace('ADMIN/', '', $menu->URLName);

            // Split the cleaned URLName by slashes to get hierarchy
            $path = explode('/', $cleanUrlName);

            // Build the admin menu tree based on the split path
            self::insertIntoAdminTree($menuTree, $path, $menu);
        }

        return $menuTree;
    }

    // Recursive function to insert items into the admin menu tree
    private static function insertIntoAdminTree(&$tree, $path, $menu)
    {
        $current = array_shift($path);

        // Check if the current part of the path exists in the tree
        if (!isset($tree[$current])) {
            $tree[$current] = [
                'name' => $current,
                'children' => [],
                'url' => count($path) == 0 ? $menu->URLName : null, // If it's the last part, set the URL
                'route_name' => count($path) == 0 ? self::generateAdminRouteName($menu->URLName) : null, // Generate the admin route name
            ];
        }

        // If there are more parts to the path, continue inserting into children
        if (count($path) > 0) {
            self::insertIntoAdminTree($tree[$current]['children'], $path, $menu);
        }
    }

    // Function to generate the route name by replacing "/" with "." and appending ".index"
    private static function generateAdminRouteName($urlName)
    {
        // Generate the admin route name with 'ADMIN/' prefix
        $routeName = str_replace('ADMIN/', 'admin.', $urlName);

        // Replace "/" with "."
        $routeName = str_replace('/', '.', $routeName);

        // Append ".index" to the admin route name
        return strtolower($routeName);
    }

    public static function getBranchMenus() {
        // Fetch menus where AppMenuName starts with 'BRANCH/' for SPC Portal Application
        $menus = DB::connection('access')
                    ->table('tblmenu as m')
                    ->leftJoin('tblpermissions as p', function($join) {
                        $join->on('m.MenuID', '=', 'p.MenuID')
                            ->where('p.ApplicationID', config('app.software_id'))
                            ->where('p.PositionID', Auth::user()->PositionID)
                            ->where('p.WithPermission', 1);
                    })
                    ->where('m.ApplicationID', config('app.software_id'))
                    ->where('m.AppMenuName', 'like', 'BRANCH/%') // Filter by AppMenuName starting with 'BRANCH/'
                    ->where('p.PositionID', Auth::user()->PositionID)
                    ->where('p.WithPermission', 1)
                    ->select('m.MenuID', 'm.AppMenuName', 'm.AppMenuID', 'm.URLName')
                    ->orderBy('m.AppMenuID')
                    ->get();

        return $menus;
    }

    // public static function getBranchMenuItems() {
    //     // Fetch menus where AppMenuName starts with 'BRANCH/' for SPC Portal Application
    //     $menus = DB::connection('access')
    //                 ->table('tblmenu as m')
    //                 ->leftJoin('tblpermissions as p', function($join) {
    //                     $join->on('m.MenuID', '=', 'p.MenuID')
    //                         ->where('p.ApplicationID', config('app.software_id'))
    //                         ->where('p.PositionID', Auth::user()->PositionID)
    //                         ->where('p.WithPermission', 1);
    //                 })
    //                 ->where('m.ApplicationID', config('app.software_id'))
    //                 ->where('m.AppMenuName', 'like', 'BRANCH/%') // Filter by AppMenuName starting with 'BRANCH/'
    //                 ->where('p.PositionID', Auth::user()->PositionID)
    //                 ->where('p.WithPermission', 1)
    //                 ->select('m.MenuID', 'm.AppMenuName', 'm.AppMenuID', 'm.URLName')
    //                 ->orderBy('m.AppMenuID')
    //                 ->get();

    //     return self::buildBranchMenuTree($menus);
    // }

    // // Function to build branch menu tree using URLName as hierarchical structure
    // private static function buildBranchMenuTree($menus) {
    //     $menuTree = [];

    //     foreach ($menus as $menu) {
    //         // Remove 'BRANCH/' prefix from URLName
    //         $cleanUrlName = str_replace('BRANCH/', '', $menu->URLName);

    //         // Split the cleaned URLName by slashes to get hierarchy
    //         $path = explode('/', $cleanUrlName);

    //         // Build the branch menu tree based on the split path
    //         self::insertIntoBranchTree($menuTree, $path, $menu);
    //     }

    //     return $menuTree;
    // }

    // // Recursive function to insert items into the branch menu tree
    // private static function insertIntoBranchTree(&$tree, $path, $menu) {
    //     $current = array_shift($path);

    //     // Check if the current part of the path exists in the tree
    //     if (!isset($tree[$current])) {
    //         $tree[$current] = [
    //             'name' => $current,
    //             'children' => [],
    //             'url' => count($path) == 0 ? $menu->URLName : null, // If it's the last part, set the URL
    //             'route_name' => count($path) == 0 ? self::generateBranchRouteName($menu->URLName) : null, // Generate the branch route name
    //         ];
    //     }

    //     // If there are more parts to the path, continue inserting into children
    //     if (count($path) > 0) {
    //         self::insertIntoBranchTree($tree[$current]['children'], $path, $menu);
    //     }
    // }

    // // Function to generate the branch route name by replacing "/" with "." and appending ".index"
    // private static function generateBranchRouteName($urlName) {
    //     // Generate the branch route name with 'BRANCH/' prefix
    //     $routeName = str_replace('BRANCH/', 'user.', $urlName);

    //     // Replace "/" with "."
    //     $routeName = str_replace('/', '.', $routeName);

    //     // Append ".index" to the route name
    //     return strtolower($routeName . '.index');
    // }

    public static function totalPermissions() {
        $total = DB::connection('access')
                ->table('tblmenu as m')
                ->leftJoin('tblpermissions as p', function($join) {
                    $join->on('m.MenuID', '=', 'p.MenuID')
                        ->where('p.ApplicationID', config('app.software_id'))
                        ->where('p.PositionID', Auth::user()->PositionID)
                        ->where('p.WithPermission', 1);
                })
                ->where('m.ApplicationID', config('app.software_id'))
                ->where('p.PositionID', Auth::user()->PositionID)
                ->where('p.WithPermission', 1)
                ->count();


        return $total;
    }
}
