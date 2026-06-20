<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PedidoController;
use App\Http\Controllers\Admin\PagamentoController;
use App\Http\Controllers\Admin\NoticiaController;
use App\Http\Controllers\Admin\PerfilController;
use App\Http\Controllers\Admin\RelatorioController;

// O RouteServiceProvider já aplica: middleware web + auth + admin, prefix admin, name admin.
// Aqui definimos apenas os endpoints internos.

Route::get("/dashboard", [DashboardController::class, "index"])->name("dashboard");

// Perfil
Route::get("/perfil", [PerfilController::class, "editar"])->name("perfil.editar");
Route::put("/perfil", [PerfilController::class, "atualizar"])->name("perfil.atualizar");
Route::put("/perfil/senha", [PerfilController::class, "atualizarSenha"])->name("perfil.senha");

// Pedidos
Route::prefix("pedidos")->name("pedidos.")->group(function () {
    Route::get("/", [PedidoController::class, "index"])->name("index");
    Route::get("/{pedido}", [PedidoController::class, "show"])->name("show");
});


// Notícias
Route::resource("noticias", NoticiaController::class)->except(["show"]);
Route::patch("noticias/{noticia}/publicar", [NoticiaController::class, "publicar"])->name("noticias.publicar");
Route::patch("noticias/{noticia}/arquivar", [NoticiaController::class, "arquivar"])->name("noticias.arquivar");

// Relatórios — descomentar quando o módulo estiver implementado
Route::prefix("relatorios")->name("relatorios.")->group(function () {
    Route::get("/pedidos", [RelatorioController::class, "index"])->name("pedidos");
});

// ver pagamentos
// Pagamentos
Route::prefix("pagamentos")->name("pagamentos.")->group(function () {
    Route::get("/pendentes", [PagamentoController::class, "pendentes"])->name("pendentes");
    Route::get("/{pagamento}/comprovativo", [PagamentoController::class, "verComprovativo"])->name("ver-comprovativo");
    Route::post("/{pagamento}/aprovar", [PagamentoController::class, "aprovar"])->name("aprovar");
    Route::post("/{pagamento}/rejeitar", [PagamentoController::class, "rejeitar"])->name("rejeitar");
});