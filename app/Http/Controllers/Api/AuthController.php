<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddClientRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\Package;
use App\Models\Prospect;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(UserRegisterRequest $request)
    {
        $params = $request->all();

        $params['password'] = Hash::make($params['password']);

        $user = User::create($params);

        $user->sendEmailVerificationNotification();

        if ($user) {
            return response()->json([
                'message' => 'Вы успешно зарегистрированы!',
                'success' => true,
                'status_code' => 201
            ], 201);
        } else {
            return response()->json([
                'message' => 'Ошибка регистрации, попробуйте еще раз',
                'success' => false,
                'status_code' => 500
            ], 500);
        }
    }

    public function addClient(AddClientRequest $request)
    {
        $params = $request->all();

        if ($request->has('partner')) {
            $partner = User::where('lr_number', $request['partner'])->first();
            if ($partner) {
                $access = Package::where('user_id', $partner->id)
                    ->where('instrument', 'yes')
                    ->first();

                if ($access) {
                    $params['user_id'] = $partner->id;
                } else {
                    $params['user_id'] = 2;
                }
            } else {
                $params['user_id'] = 2;
            }
        } else {
            $params['user_id'] = 2;
        }

        $user = User::where('id', $params['user_id'])->first();

        $prospect = Prospect::create($params);

        if ($prospect) {
            return response()->json([
                'lr_number' => $user->lr_number,
                'prospect_id' => $prospect->id,
                'message' => 'Успешно',
                'success' => true,
                'status_code' => 201
            ], 201);
        } else {
            return response()->json([
                'message' => 'Ошибка, попробуйте еще раз',
                'success' => false,
                'status_code' => 500
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProspect(Request $request, $id)
    {
        $params = $request->all();

        $prospect = Prospect::find($id);

        if ($prospect->update($params)) {
            return response()->json([
                'prospect' => $prospect,
                'message' => 'Результаты теста обновлены',
                'success' => true,
                'status_code' => 200
            ], 200);
        } else {
            return response()->json([
                'message' => 'Ошибка обновления, попробуйте снова',
                'success' => false,
                'status_code' => 500
            ], 500);
        }
    }

    public function login(UserLoginRequest $request)
    {
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json([
                'message' => 'Неверный email или пароль',
                'status_code' => 401
            ], 401);
        }

        $user = User::where('email', $request->email)->where('email_verified_at', '<>', NULL)->first();

        if (!$user) {
            return response()->json([
                "message" => 'Email не подтвержден',
                'success' => false,
                'status_code' => 500
            ], 500);
        }

//        $user = $request->user();

        if ($user->role == 'admin') {
            $tokenData = $user->createToken('Personal Access Tokens', ['admin']);
        } else if ($user->role == 'guest') {
            $tokenData = $user->createToken('Personal Access Tokens', ['guest']);
        } else {
            $tokenData = $user->createToken('Personal Access Tokens', ['partner']);
        }

        $token = $tokenData->token;

        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        if ($token->save()) {
            return response()->json([
                'user' => $user,
                'access_token' => $tokenData->accessToken,
                'token_type' => 'Bearer',
                'token_scope' => $tokenData->token->scopes[0],
                'expires_at' => Carbon::parse($tokenData->token->expires_at)->toDateTimeString(),
                'status_code' => 200
            ], 200);
        } else {
            return response()->json([
                'message' => 'Что-то пошло не так, попробуйте снова',
                'status_code' => 500
            ], 500);
        }
    }

    public function logout(Request $request) {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Вы вышли из системы',
            'status_code' => 200
        ], 200);
    }

    public function profile(Request $request) {
        if ($request->user()) {
            $userId = $request->user()->id;
            $profile = User::find($userId);
            return response()->json($profile, 200);
        }

        return response()->json([
            'message' => 'Вы не вошли в систему',
            'status_code' => 500
        ], 500);
    }
}
