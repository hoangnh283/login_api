<?php

namespace Hoangnh283\Loginapi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Hoangnh283\Loginapi\Models\TelegramUser;
// use Hoangnh283\Loginapi\Models\User;
class TelegramLoginController extends Controller
{
    public function handleTelegramCallback(Request $request) {
        $telegramUser = \Laravel\Socialite\Facades\Socialite::driver('telegram')->user();
        if($telegramUser){
                $existingUser = User::where('telegram_id', $telegramUser->id)->first();
                $isRegistered = false;
                if ($existingUser) {
                    $isRegistered = true;
                    Auth::login($existingUser);
                } else {
                    // $newUser = User::create([
                    //     'name' => $telegramUser->name,
                    //     'email' => $telegramUser->id.'@telegram.com',
                    //     'password' => $telegramUser->id,
                    //     'telegram_id' => $telegramUser->id,
                    //     'telegram_username' => $telegramUser->nickname,
                    //     'telegram_photo_url' => $telegramUser->avatar,
                    // ]);
                    // Auth::login($newUser);
                    TelegramUser::create([
                        'telegram_id' => $telegramUser->id,
                    ]);
                }
            }
            return response()->json(['telegramUser' => $telegramUser, 'isRegistered'=>$isRegistered ]);
        }
    
    public function handleTelegramCallbackConnective(Request $request) {
        $telegramUser = \Laravel\Socialite\Facades\Socialite::driver('telegram')->user();
        
        if($telegramUser){
            $user = Auth::user();
            if ($user) {
                $user->telegram_id = $telegramUser->id;
                $user->telegram_username = $telegramUser->nickname;
                $user->telegram_photo_url = $telegramUser->avatar;
                $user->save();
            }
            return response()->json(['telegramUser' => $telegramUser]);
        }
    }

    public function handleTelegramCreateUser(Request $request)
    {
        if (User::where('email', $request->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'The email has already been taken.',
            ]);
        }
        $existingUser = User::where('telegram_id', $request->telegram_id)->first();
        $telegramUser = TelegramUser::where('telegram_id', $request->telegram_id)->first();
        if ($telegramUser && !$existingUser) {
            try{
                $newUser = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'telegram_id' => $request->telegram_id,
                ]);
                $telegramUser->delete();
            } catch (\Exception $e) {
                return response()->json(['error' => 'User creation error'], 404);
            }
            return response()->json(['newUser' => $newUser]);
        }else{
            return response()->json(['error' => 'telegram_id is incorrect or already exists']);
        }

    }
}
