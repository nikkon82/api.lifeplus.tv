<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProspectController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Api\VerificationController;

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

Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register'])->name('api.register');
    Route::post('login', [AuthController::class, 'login'])->name('api.login');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('api.forgot-password');
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('api.reset-password');
    Route::post('email-verification', [VerificationController::class, 'verify'])->name('api.email.verification');
    Route::post('email-verification-resend', [VerificationController::class, 'resendVerificationEmail'])->name('api.email.verification.resend');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('logout', [AuthController::class, 'logout'])->name('api.logout');
        Route::get('profile', [AuthController::class, 'profile'])->name('api.profile');
    });

    Route::post('add-client', [AuthController::class, 'addClient'])->name('api.client.add');
    Route::post('update-prospect/{prospect}', [AuthController::class, 'updateProspect'])->name('auth.update.prospect');
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::group(['prefix' => 'admin'], function () {
        Route::group(['middleware' => 'scope:admin'], function () {
            Route::post('users/restore/{user}', [UserController::class, 'restore'])->name('users.restore');
            Route::post('users/{user}/avatar', [UserController::class, 'updateAvatar'])->name('users.update.avatar');
            Route::post('users/{user}/photo_money', [UserController::class, 'updatePhotoMoney'])->name('users.update.photo_money');
            Route::post('users/{user}/photo_auto', [UserController::class, 'updatePhotoAuto'])->name('users.update.photo_auto');
            Route::post('users/{user}/password', [UserController::class, 'changePassword'])->name('users.change.password');
            Route::post('users/{user}/user-access', [UserController::class, 'updateUserAccess'])->name('users.update.access');
            Route::get('user-package/{user}', [UserController::class, 'userPackage'])->name('user.package');
            Route::get('user-access/{user}', [UserController::class, 'userAccess'])->name('user.access');
            Route::apiResource('users', UserController::class);

            Route::post('prospects/restore/{prospect}', [ProspectController::class, 'restore'])->name('prospect.restore');
            Route::apiResource('prospects', ProspectController::class);
        });
    });

    Route::group(['prefix' => 'profile'], function () {
        Route::post('update/{user}', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('update/{user}/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update.avatar');
        Route::post('update/{user}/photo_money', [ProfileController::class, 'updatePhotoMoney'])->name('profile.update.photo_money');
        Route::post('update/{user}/photo_auto', [ProfileController::class, 'updatePhotoAuto'])->name('profile.update.photo_auto');
        Route::post('update/{user}/password', [ProfileController::class, 'changePassword'])->name('profile.update.password');
        Route::get('prospects', [ProfileController::class, 'prospects'])->name('profile.prospects');
        Route::get('prospects-basket', [ProfileController::class, 'prospectsBasket'])->name('profile.prospects.basket');
		Route::post('update/prospect/{prospect}', [ProfileController::class, 'updateProspect'])->name('profile.update.prospect');
		Route::delete('prospect-delete/{prospect}', [ProfileController::class, 'destroy'])->name('profile.delete.prospect');
		Route::post('prospect-restore/{prospect}', [ProfileController::class, 'restore'])->name('profile.restore.prospect');
        Route::get('prospect-page/{prospect}', [ProfileController::class, 'index'])->name('profile.prospect.page');
        // Route::post('prospect/update/{prospect}', [ProfileController::class, 'prospects'])->name('profile.prospect.update');
        Route::get('prospects/today', [ProfileController::class, 'prospectsToday'])->name('profile.prospects.today');
        Route::get('prospects/count', [ProfileController::class, 'prospectsCount'])->name('profile.prospects.count');
        Route::get('access', [ProfileController::class, 'profileAccess'])->name('profile.access');
    });
});

Route::get('partner-access/{user}', [ProfileController::class, 'partnerAccess'])->name('api.partner.access');
