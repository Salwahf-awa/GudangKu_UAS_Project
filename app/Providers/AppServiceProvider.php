<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Database hosting (Wasmer) membuka koneksi dengan autocommit mati,
        // sehingga perubahan data tidak pernah tersimpan. Nyalakan di production.
        if ($this->app->environment('production')) {
            try {
                DB::connection()->getPdo()->exec('SET autocommit=1');
            } catch (\Throwable $e) {
                // abaikan jika database belum tersedia (misalnya saat build)
            }
        }
    }
}