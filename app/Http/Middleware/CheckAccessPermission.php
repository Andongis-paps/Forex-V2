<?php

namespace App\Http\Middleware;

use App\Models\Menu;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CheckAccessPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $appMenuName
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $AppMenuName, $PermissionType)
    {
        // Retrieve the menu by AppMenuName
        $Menu = Menu::findByAppMenuName($AppMenuName);

        if (!$Menu) {
            abort(404, 'Page not found');
        }

        $MenuID = $Menu->MenuID;

        // Check the authorization based on the permission type
        switch ($PermissionType) {
            case 'VIEW':
                // Check if the user is allowed to access the page
                if (!Gate::allows('access-permission', $MenuID)) {
                    // abort(403, 'Unauthorized');
                    return response()->view('template.404');
                }
                break;

            case 'ADD':
                // Check if the user is allowed to add an entry
                if (!Gate::allows('add-permission', $MenuID)) {
                    // abort(403, 'Unauthorized');
                    return response()->view('template.404');
                }
                break;

            case 'EDIT':
                // Check if the user is allowed to edit an entry
                if (!Gate::allows('edit-permission', $MenuID)) {
                    // abort(403, 'Unauthorized');
                    return response()->view('template.404');
                }
                break;

            case 'DELETE':
                // Check if the user is allowed to delete an entry
                if (!Gate::allows('delete-permission', $MenuID)) {
                    // abort(403, 'Unauthorized');
                    return response()->view('template.404');
                }
                break;

            case 'FILTER':
                // Check if the user is allowed to filter data
                if (!Gate::allows('filter-permission', $MenuID)) {
                    // abort(403, 'Unauthorized');
                    return response()->view('template.404');
                }
                break;

            case 'PRINT':
                // Check if the user is allowed to filter data
                if (!Gate::allows('print-permission', $MenuID)) {
                    // abort(403, 'Unauthorized');
                    return response()->view('template.404');
                }
                break;

            case 'ACKNOWLEDGE':
                // Check if the user is allowed to filter data
                if (!Gate::allows('acknowledge-permission', $MenuID)) {
                    // abort(403, 'Unauthorized');
                    return response()->view('template.404');
                }
                break;

            case 'ARCHIVE':
                // Check if the user is allowed to filter data
                if (!Gate::allows('archive-permission', $MenuID)) {
                    // abort(403, 'Unauthorized');
                    return response()->view('template.404');
                }
                break;

            default:
                // Handle unknown permission type
                abort(400, 'Invalid permission type');
                break;
        }

        // Pass the MenuID to the request for further use
        $request->attributes->set('MenuID', $MenuID);

        return $next($request);
    }
}
