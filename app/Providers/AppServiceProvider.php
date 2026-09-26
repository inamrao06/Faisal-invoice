<?php
namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Super Admin only
        Gate::define('manage-companies', fn (User $user) => $user->isGlobalAdmin());
        Gate::define('manage-branches',  fn (User $user) => $user->isGlobalAdmin());
        Gate::define('manage-settings',  fn (User $user) => $user->isGlobalAdmin());

        // Super Admin OR Admin with full access (role_num = 0)
        Gate::define('manage-users',     fn (User $user) => $user->hasFullBranchAccess());
        Gate::define('approve-records',  fn (User $user) => $user->hasFullBranchAccess());

        // Any admin scoped to a company
        Gate::define('company-area',     fn (User $user) => $user->isCompanyAdmin());
    }
}
