<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\EvaluadorController;
use App\Http\Controllers\OficinaController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\UnidadController;
use App\Models\oficina;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', [LoginController::class, 'logout']);

    Route::get('user', [UserController::class, 'current']);

    Route::patch('settings/profile', [ProfileController::class, 'update']);
    Route::patch('settings/password', [PasswordController::class, 'update']);
});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [RegisterController::class, 'register']);

    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [ResetPasswordController::class, 'reset']);

    Route::post('email/verify/{user}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend']);

    Route::post('oauth/{driver}', [OAuthController::class, 'redirect']);
    Route::get('oauth/{driver}/callback', [OAuthController::class, 'handleCallback'])->name('oauth.callback');
});


Route::group(['middleware' => 'auth:api'], function () {
    Route::get('meza-documentos',[DocumentoController::class, 'documentos']);
    Route::post('add-documento',[DocumentoController::class,'add_documento']);
    //ad documento desde unidades 
    Route::post('add-documento-unidad',[UnidadController::class,'add_documento_unidad']);

    Route::get('datos_doc_meza/{id}',[DocumentoController::class,'dato_doc']);

    Route::post('derivar-doc',[DocumentoController::class,'derrivar_doc']);

    Route::get('fetch-oficinas',[OficinaController::class,'fetch_oficinas']);

    Route::post('agregar-tiempo-busqueda/{id}',[DocumentoController::class,'agregar_tiempo_doc']);
    //unidad organida

    Route::get('unidad-documentos/{id}',[UnidadController::class,'fetch_docs']);
    Route::post('recepcionar-doc',[UnidadController::class,'recepcionar_doc']);

    Route::post('actualizar-doc/{id}',[UnidadController::class,'cambiar_doc']);

    Route::post('archivar-doc',[UnidadController::class,'archivar_doc']);
    Route::post('resolver-doc',[UnidadController::class,'resolver_doc']);

    //administrado

    Route::get('get-users',[AdminController::class,'getusers']);
    Route::get('get-roles',[AdminController::class,'roles']);
    Route::get('get-oficinas',[AdminController::class,'oficinas']);
    Route::post('add-user',[AdminController::class,'add_user']);
    Route::post('edit-user/{id}',[AdminController::class,'editar_user']);

    Route::get('get-documentos-rep',[AdminController::class,'documentos_rep']);
    Route::post('buscar-fechas',[AdminController::class,'buscar_fechas']);
    Route::post('add-oficina',[AdminController::class,'add_oficina']);

    Route::post('exportar-excel',[AdminController::class,'exportar_docs']);
    Route::post('exportar-excel-seguimientos',[AdminController::class,'exportar_docs_seguimiento']);
    Route::post('exportar-excel-seguimientos-oficinas',[AdminController::class,'exportar_docs_seguimiento_ofic']);
    Route::post('exportar-excel-creaciones',[AdminController::class,'exportar_docs_tiempos']);
    Route::post('exportar-seguimientos',[AdminController::class,'exportar_seguimientos']);


    Route::get('all-oficinas',[OficinaController::class,'oficinas']);
    Route::post('cambiar-estado-oficina',[OficinaController::class,'CambiarEstado']);


    Route::get('documentos-archivo',[AdminController::class,'documentos_archivo']);
    Route::post('eliminar-derivacion',[AdminController::class,'eliminar_derivacion']);

    Route::post('cambiar-datos-doc',[DocumentoController::class, 'cambiar_datos']);

    Route::get('obtener-numero',[UnidadController::class,'obtener_numero']);
    //a
    Route::post('imprimir',[UnidadController::class,'imprimir']);

});