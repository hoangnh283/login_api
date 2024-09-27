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
use Hoangnh283\Loginapi\Models\User as UserPackage;
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
                    $existingUser = TelegramUser::where('telegram_id', $telegramUser->id)->first();
                    if(!$existingUser){
                        try{
                            TelegramUser::create([
                                'telegram_id' => $telegramUser->id,
                            ]);
                        }catch (\Exception $e) {
                            return response()->json(['error' => $e->getMessage()], 404);
                        }
                    }
                }
            }
            return response()->json(['telegramUser' => $telegramUser, 'isRegistered'=>$isRegistered ]);
        }
    
    public function handleTelegramCallbackConnective(Request $request) {
        // $allParams = $request->all();
        // $customParam = $allParams['test'] ?? null;
        // unset($allParams['test']);

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

        // var_dump(User::where('email', $request->email)->exists());die;

        if (User::where('email', $request->email)->exists()) {
            // throw ValidationException::withMessages([
            //     'email' => 'The email has already been taken.',
            // ]);
            return response()->json(['error' => 'The email has already been taken'],400);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8', 
            'telegram_id' => 'required|string', 
        ]);
        $existingUser = User::where('telegram_id', $request->telegram_id)->first();
        $telegramUser = TelegramUser::where('telegram_id', $request->telegram_id)->first();
        if ($telegramUser && !$existingUser) {
            try{
                $newUser = UserPackage::create([
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'],
                    'password' => Hash::make($validatedData['password']),
                    'telegram_id' => $validatedData['telegram_id'],
                ]);
                // $telegramUser->delete();
            } catch (\Exception $e) {
                return response()->json(['error' => 'User creation error'], 404);
            }
            return response()->json(['newUser' => $newUser]);
        }else{
            return response()->json(['error' => 'telegram_id is incorrect or already exists']);
        }

    }
}
