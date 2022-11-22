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

Route::get('meza-documentos',[DocumentoController::class, 'documentos']);
Route::post('add-documento',[DocumentoController::class,'add_documento']);
Route::get('datos_doc_meza/{id}',[DocumentoController::class,'dato_doc']);

Route::post('derivar-doc',[DocumentoController::class,'derrivar_doc']);

Route::get('fetch-oficinas',[OficinaController::class,'fetch_oficinas']);

//unidad organida

Route::get('unidad-documentos/{id}',[UnidadController::class,'fetch_docs']);
Route::post('recepcionar-doc',[UnidadController::class,'recepcionar_doc']);

Route::post('actualizar-doc/{id}',[UnidadController::class,'cambiar_doc']);
//administrado

Route::get('get-users',[AdminController::class,'getusers']);
Route::get('get-roles',[AdminController::class,'roles']);
Route::get('get-oficinas',[AdminController::class,'oficinas']);
Route::post('add-user',[AdminController::class,'add_user']);
Route::post('edit-user/{id}',[AdminController::class,'editar_user']);