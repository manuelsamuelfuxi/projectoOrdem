<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use App\Models\Pedido;
use App\Models\Noticia;
use App\Policies\AdminPolicy;
use App\Policies\PedidoPolicy;
use App\Policies\NoticiaPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => AdminPolicy::class,
        Pedido::class => PedidoPolicy::class,
        Noticia::class => NoticiaPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}