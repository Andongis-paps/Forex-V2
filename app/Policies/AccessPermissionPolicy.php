<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\DB;

class AccessPermissionPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */

    /**
     * Check if the user has a permission to specific action.
     */
    protected function checkPermission(User $user, $MenuID, $Permission)
    {
        // First, check if the WithPermission is 1
        $withPermission = DB::connection('access')
                                ->table('tblpermissions')
                                ->where('ApplicationID', config('app.software_id'))
                                ->where('MenuID', $MenuID)
                                ->where('PositionID', $user->PositionID)
                                ->value('WithPermission');

        // If WithPermission is 0, deny access regardless of the specific permission
        if ($withPermission == 0) {
            return false;
        }

        // If WithPermission is 1, check the specific permission (e.g., Add, Edit, Delete, Filter)
        return DB::connection('access')
                    ->table('tblpermissions')
                    ->where('ApplicationID', config('app.software_id'))
                    ->where('MenuID', $MenuID)
                    ->where('PositionID', $user->PositionID)
                    ->value($Permission) == 1;
    }

    /**
     * Determine if the user can access an entry.
     */
    public function access(User $user, $MenuID)
    {
        return $this->checkPermission($user, $MenuID, 'WithPermission');
    }

    /**
     * Determine if the user can add an entry.
     */
    public function add(User $user, $MenuID)
    {
        return $this->checkPermission($user, $MenuID, 'AddPermission');
    }

    /**
     * Determine if the user can edit an entry.
     */
    public function edit(User $user, $MenuID)
    {
        return $this->checkPermission($user, $MenuID, 'EditPermission');
    }

    /**
     * Determine if the user can delete an entry.
     */
    public function delete(User $user, $MenuID)
    {
        return $this->checkPermission($user, $MenuID, 'DeletePermission');
    }

    /**
     * Determine if the user can filter data.
     */
    public function filter(User $user, $MenuID)
    {
        return $this->checkPermission($user, $MenuID, 'FilterPermission');
    }

    /**
     * Determine if the user can print data.
     */
    public function print(User $user, $MenuID)
    {
        return $this->checkPermission($user, $MenuID, 'PrintPermission');
    }

    /**
     * Determine if the user can acknowledgement data.
     */
    public function acknowledge(User $user, $MenuID)
    {
        return $this->checkPermission($user, $MenuID, 'AcknowledgemnentPermission');
    }

    /**
     * Determine if the user can acrhive data.
     */
    public function archive(User $user, $MenuID)
    {
        return $this->checkPermission($user, $MenuID, 'ArchivePermission');
    }
}
