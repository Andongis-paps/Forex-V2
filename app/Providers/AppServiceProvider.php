<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\User;
use App\Policies\AccessPermissionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use DB;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        // Fetch session lifetime from the database
        $session = DB::connection('laravelsysconfigs')
            ->table('tblxsessions')
            ->select('lifetime', 'expire_on_close')
            ->where('software_id', config('app.software_id'))
            ->first();

        // Update session lifetime configuration
        if ($session) {
            config(['session.lifetime' => $session->lifetime]);
            config(['session.expire_on_close' => $session->expire_on_close]);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        $this->registerPolicies();

        // Defining gates based on the policies
        Gate::define('access-permission', [AccessPermissionPolicy::class, 'access']);
        Gate::define('add-permission', [AccessPermissionPolicy::class, 'add']);
        Gate::define('edit-permission', [AccessPermissionPolicy::class, 'edit']);
        Gate::define('delete-permission', [AccessPermissionPolicy::class, 'delete']);
        Gate::define('filter-permission', [AccessPermissionPolicy::class, 'filter']);
        Gate::define('print-permission', [AccessPermissionPolicy::class, 'print']);
        Gate::define('acknowledge-permission', [AccessPermissionPolicy::class, 'acknowledge']);
        Gate::define('archive-permission', [AccessPermissionPolicy::class, 'archive']);
    }
}
