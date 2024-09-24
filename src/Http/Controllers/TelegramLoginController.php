<?php

namespace Hoangnh283\Loginapi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
// use Hoangnh283\Loginapi\Models\User;
class TelegramLoginController extends Controller
{
    public function handleTelegramCallback(Request $request) {
        $telegramUser = \Laravel\Socialite\Facades\Socialite::driver('telegram')->user();
        // var_dump($telegramUser);die;
        if($telegramUser){
            $user = Auth::user();
                $isLogin = false;
                $isLinkToUser = false;
            if ($user) {
                $user->telegram_id = $telegramUser->id;
                $user->telegram_username = $telegramUser->nickname;
                $user->telegram_photo_url = $telegramUser->avatar;
                $user->save();
                $isLinkToUser = true;
            } else {  
                $existingUser = User::where('telegram_id', $telegramUser->id)->first();
                if ($existingUser) {
                    Auth::login($existingUser);
                    $isLogin = false;
                } else {
                    $newUser = User::create([
                        'name' => $telegramUser->name,
                        'email' => $telegramUser->id.'@telegram.com',
                        'password' => $telegramUser->id,
                        'telegram_id' => $telegramUser->id,
                        'telegram_username' => $telegramUser->nickname,
                        'telegram_photo_url' => $telegramUser->avatar,
                    ]);
                    Auth::login($newUser);
                }
                $isLogin = true;
            }
            return response()->json(['telegramUser' => $telegramUser, 'isLogin'=> $isLogin, 'isLinkToUser'=> $isLinkToUser]);
        }
    }
}
