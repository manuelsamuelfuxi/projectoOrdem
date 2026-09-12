<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Enums\TipoDocumento;
use App\Http\Controllers\Publico\HomeController;
use App\Http\Controllers\Publico\PedidoController;
use App\Http\Controllers\Publico\ConsultaController;
use App\Http\Controllers\Publico\NoticiaController;
use App\Http\Controllers\Publico\PagamentoController;
use App\Http\Controllers\Publico\MunicipioController;

Auth::routes();

// =============================================
// HOME E CONTEÚDO PÚBLICO
// =============================================
Route::get("/", [HomeController::class, "index"])->name("home");
Route::get("/noticias", [NoticiaController::class, "index"])->name("noticias.index");
Route::get("/noticias/{noticia}", [NoticiaController::class, "show"])->name("noticias.show");

// =============================================
// FORMULÁRIOS DE PEDIDO (MULTI-ETAPAS)
// =============================================
Route::get("/pedido/carteira", [PedidoController::class, "formCarteira"])->name("pedido.carteira.form");
Route::get("/pedido/licenca", [PedidoController::class, "formLicenca"])->name("pedido.licenca.form");

Route::post("/pedido/etapa1", [PedidoController::class, "salvarEtapa1"])->name("pedido.salvar-etapa1");
Route::get("/pedido/dados-profissionais", [PedidoController::class, "dadosProfissionais"])->name("pedido.dados-profissionais");
Route::post("/pedido/etapa2/salvar", [PedidoController::class, "salvarEtapa2"])->name("pedido.etapa2.salvar");
Route::get("/pedido/upload-documentos", [PedidoController::class, "uploadDocumentos"])->name("pedido.upload-documentos");
Route::post("/pedido/etapa3/salvar", [PedidoController::class, "salvarEtapa3"])->name("pedido.etapa3.salvar");
Route::delete("/pedido/etapa3/remover", [PedidoController::class, "removerDocumento"])->name("pedido.etapa3.remover");

// Submissão final — guarda na BD e redireciona para consulta.estado
Route::post("/pedido/submeter", [PedidoController::class, "submeter"])->name("pedido.submeter");

// Etapa 4 mantida para compatibilidade (opcional)
Route::get("/pedido/ficha-cobranca", [PedidoController::class, "fichaCobranca"])->name("pedido.ficha-cobranca");

// =============================================
// AJAX — MUNICÍPIOS POR PROVÍNCIA
// =============================================
Route::get("/municipios/{provinciaId}", [MunicipioController::class, "porProvincia"])
    ->name("municipios.por.provincia")
    ->whereNumber("provinciaId");

// =============================================
// CONSULTA DE PEDIDO (SEM LOGIN)
// =============================================
Route::prefix("consulta")->name("consulta.")->group(function () {
    Route::get("/", [ConsultaController::class, "form"])->name("form");

    Route::post("/", [ConsultaController::class, "consultar"])
        ->name("consultar")
        ->middleware("throttle:public-query");

    Route::get("/{uuid}/estado", [ConsultaController::class, "estado"])
        ->name("estado")
        ->where("uuid", "[0-9a-fA-F-]{36}")
        ->middleware("throttle:public-query");

    Route::get("/{uuid}/baixar-ficha-cobranca", [ConsultaController::class, "baixarFichaCobranca"])
        ->name("baixar-ficha-cobranca")
        ->where("uuid", "[0-9a-fA-F-]{36}")
        ->middleware("throttle:public-query");

    // NOVO — download da carteira profissional já emitida
    Route::get("/{uuid}/baixar-carteira", [ConsultaController::class, "baixarCarteira"])
        ->name("baixar-carteira")
        ->where("uuid", "[0-9a-fA-F-]{36}")
        ->middleware("throttle:public-query");

    Route::get("/verificar", [ConsultaController::class, "verificarPorQrCode"])
        ->name("verificar")
        ->middleware("throttle:public-query");
});

// =============================================
// UPLOAD DE COMPROVATIVO (APÓS CONSULTA)
// =============================================
Route::prefix("pedido")->name("pedido.")->group(function () {
    Route::get("/{uuid}/upload-comprovativo", [ConsultaController::class, "formUpload"])
        ->name("form-upload")
        ->where("uuid", "[0-9a-fA-F-]{36}")
        ->middleware("throttle:public-query");

    Route::post("/{uuid}/enviar-comprovativo", [ConsultaController::class, "enviarComprovativo"])
        ->name("enviar-comprovativo")
        ->where("uuid", "[0-9a-fA-F-]{36}")
        ->middleware("throttle:public-submit");

    Route::get("/{uuid}/baixar/{tipo}", [ConsultaController::class, "baixarDocumento"])
        ->name("baixar-documento")
        ->where("uuid", "[0-9a-fA-F-]{36}")
        ->whereIn("tipo", array_column(TipoDocumento::cases(), "value"))
        ->middleware("signed");
});

// =============================================
// PROCESSAMENTO DE PAGAMENTO
// =============================================
Route::prefix("pagamento")->name("pagamento.")->group(function () {
    Route::get("/processar/{id}", [PagamentoController::class, "processar"])->name("processar")->whereNumber("id");
});

// =============================================
// PÁGINAS INSTITUCIONAIS
// =============================================
Route::get("/sobre", fn() => view("publico.sobre"))->name("sobre");
Route::get("/contactos", fn() => view("publico.contactos"))->name("contactos");
Route::get("/legislacao", fn() => view("publico.legislacao"))->name("legislacao");

// =============================================
// PREVIEW DE DOCUMENTO (OPCIONAL)
// =============================================
// Mesmo raciocínio: {tipo} restringido ao enum directamente na rota.
Route::get("/pedido/preview/{tipo}", [PedidoController::class, "previewDocumento"])
    ->name("pedido.preview")
    ->whereIn("tipo", array_column(TipoDocumento::cases(), "value"));

Route::get("/pedido/cartao-membro", [PedidoController::class, "formCartaoMembro"])->name("pedido.cartao-membro.form");