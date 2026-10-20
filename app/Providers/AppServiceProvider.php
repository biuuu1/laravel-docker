<?php

namespace App\Providers;

use App\Contracts\RepositoriMember;
use App\Contracts\RepositoriProduk;
use App\Contracts\RepositoriTransaksi;
use App\Repositories\RepositoriMemberArray;
use App\Repositories\RepositoriProdukEloquent;
use App\Repositories\RepositoriTransaksiEloquent;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            RepositoriProduk::class,
            RepositoriProdukEloquent::class
        );

        $this->app->bind(
            RepositoriMember::class,
            RepositoriMemberArray::class
        );

        $this->app->singleton(
            RepositoriTransaksi::class,
            RepositoriTransaksiEloquent::class
        );
    }

    public function boot(): void
    {
        //
    }
}
