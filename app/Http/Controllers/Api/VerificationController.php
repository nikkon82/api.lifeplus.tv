<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify(Request $request) {
        $user = User::findOrFail($request->id);

        // do this check, only if you allow unverified user to login
//        if (! hash_equals((string) $request->id, (string) $request->user()->getKey())) {
//            throw new AuthorizationException;
//        }

        if (! hash_equals((string) $request->hash, sha1($user->getEmailForVerification()))) {
            return response()->json([
                "message" => "Неавторизованный",
                "success" => false
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                "message" => "Пользователь уже проверен!",
                "success" => false
            ]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json([
            "message" => "Email успешно проверен!",
            "success" => true
        ]);
    }

    public function resendVerificationEmail (Request $request) {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                "message" => "Ошибка отправки!",
                "success" => false
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            "message" => "Проверьте ваш Email!",
            "success" => true
        ]);
    }
}
