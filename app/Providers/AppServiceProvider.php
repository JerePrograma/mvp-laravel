<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;    // ← para hasTable()
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Sólo intentamos crear el admin si la tabla ya está migrada
        if (Schema::hasTable('users')) {
            User::firstOrCreate(
                ['email' => 'admin@admin.com'],
                [
                    'name' => 'admin',
                    'password' => Hash::make('Admin!1234'),
                    'is_admin' => true,
                ]
            );
        }
    }
}
