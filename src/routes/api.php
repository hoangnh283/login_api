<?php

use Illuminate\Support\Facades\Route;
use Hoangnh283\Loginapi\Http\Controllers\TelegramLoginController;
use Illuminate\Support\Facades\Auth;

Route::middleware(['web'])->group(function () {
    if (Auth::check()) {
        config(['services.telegram.redirect' => 'callback_connective']);
    }
    Route::prefix('api/telegram')->group(function () {
        Route::get('redirect',function (){

            return \Laravel\Socialite\Facades\Socialite::driver('telegram')->redirect(); 
        });
        Route::get('callback',[TelegramLoginController::class, 'handleTelegramCallback']);
        Route::get('callback_connective',[TelegramLoginController::class, 'handleTelegramCallbackConnective']);
        Route::post('create_user', [TelegramLoginController::class, 'handleTelegramCreateUser']);

        //         Route::get('callback',function (){
        //     $telegramUser =  \Laravel\Socialite\Facades\Socialite::driver('telegram')->user(); 
        //     dd($telegramUser);die;
        // });
    });

    Route::get('logout', function () {
        \Illuminate\Support\Facades\Auth::logout();
        return redirect('/');
    });
});
