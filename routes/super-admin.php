<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\AdminController;
use App\Http\Controllers\SuperAdmin\PedidoController;
use App\Http\Controllers\SuperAdmin\RelatorioController;
use App\Http\Controllers\SuperAdmin\PerfilController;
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
    Route::post("/{pedido}/rejeitar", [PedidoController::class, "rejeitar"])->name("rejeitar");
});

// Licenças (geração/visualização do documento em si)
Route::prefix("licencas")->name("licencas.")->group(function () {
    Route::get("/{pedido}/preview", [LicencaController::class, "preview"])->name("preview");
    Route::get("/{pedido}/documento", [LicencaController::class, "visualizarDocumento"])->name("documento");
});

// Relatórios
Route::prefix("relatorios")->name("relatorios.")->group(function () {
    Route::get("/completos", [RelatorioController::class, "completos"])->name("completos");
});