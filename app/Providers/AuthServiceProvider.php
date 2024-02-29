<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    const ROLES_ADMIN = 1;
    const ROLES_GURU = 2;
    const ROLES_SISWA = 3;
    const ROLES_TIM_PPDB = 4;

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Gate::define('is_admin', function (User $user) {
            if ($user->role_id === self::ROLES_ADMIN) {
                return true;
            }
            throw new AuthorizationException('Akses ditolak.');
        });
        Gate::define('is_guru', function (User $user) {
            if ($user->role_id === self::ROLES_GURU) {
                return true;
            }
            throw new AuthorizationException('Akses ditolak.');
        });
        Gate::define('is_siswa', function (User $user) {
            if ($user->role_id === self::ROLES_SISWA) {
                return true;
            }
            throw new AuthorizationException('Akses ditolak.');
        });
        Gate::define('is_tim_ppdb', function (User $user) {
            if ($user->role_id === self::ROLES_TIM_PPDB) {
                return true;
            }
            throw new AuthorizationException('Akses ditolak.');
        });
        Gate::define('is_siswa_or_guru', function (User $user) {
            if ($user->role_id === self::ROLES_SISWA || $user->role_id === self::ROLES_GURU) {
                return true;
            }
        });
    }
}
