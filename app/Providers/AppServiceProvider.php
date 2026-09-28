<?php

namespace App\Providers;

use App\Contracts\RepositoriMember;
use App\Contracts\RepositoriProduk;
use App\Contracts\RepositoriTransaksi;
use App\Repositories\RepositoriMemberArray;
use App\Repositories\RepositoriProdukArray;
use App\Repositories\RepositoriTransaksiBerkas;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            RepositoriProduk::class,
            RepositoriProdukArray::class
        );

        $this->app->bind(
            RepositoriMember::class,
            RepositoriMemberArray::class
        );

        $this->app->singleton(
            RepositoriTransaksi::class,
            RepositoriTransaksiBerkas::class
        );
    }

    public function boot(): void
    {
        //
    }
}
