<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\AdminController;
use App\Http\Controllers\SuperAdmin\PedidoController;
use App\Http\Controllers\SuperAdmin\RelatorioController;
use App\Http\Controllers\SuperAdmin\PerfilController;
use App\Http\Controllers\SuperAdmin\CarteiraController;
use App\Http\Controllers\SuperAdmin\LicencaController;

Route::get("/dashboard", [DashboardController::class, "index"])->name("dashboard");

// Perfil
Route::get("/perfil", [PerfilController::class, "editar"])->name("perfil.editar");
Route::put("/perfil", [PerfilController::class, "atualizar"])->name("perfil.atualizar");
Route::put("/perfil/senha", [PerfilController::class, "atualizarSenha"])->name("perfil.senha");

// Admins
Route::prefix("admins")->name("admins.")->group(function () {
    Route::get("/", [AdminController::class, "index"])->name("index");
    Route::get("/criar", [AdminController::class, "create"])->name("create");
    Route::post("/", [AdminController::class, "store"])->name("store");
    Route::get("/{admin}/editar", [AdminController::class, "edit"])->name("edit");
    Route::put("/{admin}", [AdminController::class, "update"])->name("update");
    Route::delete("/{admin}", [AdminController::class, "destroy"])->name("destroy");
});

// Pedidos (fluxo do pedido)
Route::prefix("pedidos")->name("pedidos.")->group(function () {
    Route::get("/aprovados-financeiramente", [PedidoController::class, "aprovadosFinanceiramente"])->name("financeiramente-aprovados");
    Route::post("/{pedido}/aprovar-emissao", [PedidoController::class, "aprovarEmissao"])->name("aprovar-emissao");

    // NOVO — emitir em massa todos os pedidos pendentes de emissão
    Route::post("/aprovar-emissao-todas", [PedidoController::class, "aprovarEmissaoTodas"])->name("aprovar-emissao-todas");

    Route::post("/{pedido}/rejeitar", [PedidoController::class, "rejeitar"])->name("rejeitar");
});

// Carteira (geração/visualização do documento em si)
Route::prefix("carteira")->name("carteira.")->group(function () {
    // NOVO — junta todas as carteiras emitidas num único PDF e mostra inline
    Route::get("/visualizar-todas", [CarteiraController::class, "visualizarTodas"])->name("visualizar-todas");

    Route::get("/{pedido}/preview", [CarteiraController::class, "preview"])->name("preview");
    Route::get("/{pedido}/documento", [CarteiraController::class, "visualizarDocumento"])->name("documento");
});

// Licença (documento em construção — rota provisória, só para visualizar o PDF durante o desenvolvimento)
Route::prefix("licenca")->name("licenca.")->group(function () {
    Route::get("/{pedido}/preview", [LicencaController::class, "preview"])->name("preview");
});

// Relatórios
Route::prefix("relatorios")->name("relatorios.")->group(function () {
    Route::get("/completos", [RelatorioController::class, "completos"])->name("completos");
});