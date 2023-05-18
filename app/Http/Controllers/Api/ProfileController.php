<?php

namespace App\Http\Controllers\Api;

use App\Actions\User\UpdateUserPasswordAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeUserPasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateProspectRequest;
use App\Models\Package;
use App\Models\Prospect;
use App\Models\User;
use App\Traits\UploadImageTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    use UploadImageTrait;

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $id)
    {
        $user = $request->user();

        $prospect = Prospect::withTrashed()->where('user_id', $user->id)->find($id);

        return response()->json($prospect, 200);
    }

    /**
     * all user instruments
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileAccess(Request $request)
    {
        $user = $request->user();

        $columns = ['instrument'];
        $userAccess = Package::select($columns)
            ->where('user_id', $user->id)
            ->first();

        return response()->json($userAccess, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param $lr_number
     * @return \Illuminate\Http\JsonResponse
     */
    public function partnerAccess($lr_number)
    {
        if ($lr_number) {
            $user = User::where('lr_number', $lr_number)->first();

            if ($user) {
                $access = Package::where('user_id', $user->id)
                    ->where('instrument', 'yes')
                    ->first();

                if ($access) {
                    return response()->json($user, 200);
                } else {
                    $user = User::where('id', 2)->first();
                    return response()->json($user, 200);
                }
            } else {
                $user = User::where('id', 2)->first();
                return response()->json($user, 200);
            }
        } else {
            $user = User::where('id', 2)->first();
            return response()->json($user, 200);
        }
    }

    /**
     * User's prospects
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function prospects(Request $request) {
        if ($request->user()) {
            $user = $request->user();

            $columns = ['id', 'name', 'phone', 'phone_whatsapp', 'phone_viber','created_at', 'action_bot', 'instrument', 'step', 'result'];
            $prospects = Prospect::select($columns)
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($prospects, 200);
        }

        return response()->json([
            'message' => 'Not logged in',
            'success' => false,
            'status_code' => 500
        ], 500);
    }

    public function prospectsBasket(Request $request) {
        if ($request->user()) {
            $user = $request->user();

            $columns = ['id', 'name', 'phone', 'phone_whatsapp', 'phone_viber','created_at', 'action_bot', 'instrument', 'step', 'result'];
            $prospects = Prospect::select($columns)
                ->onlyTrashed()
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($prospects, 200);
        }

        return response()->json([
            'message' => 'Not logged in',
            'success' => false,
            'status_code' => 500
        ], 500);
    }

	/**
     * Update the specified resource in storage.
     *
     * @param  UpdateProspectRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProspect(UpdateProspectRequest $request, $id)
    {
        $params = $request->all();

        $prospect = Prospect::find($id);

        if ($prospect->update($params)) {
            return response()->json([
                'prospect' => $prospect,
                'message' => 'Заявка успешно обновлена',
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function prospectsToday(Request $request)
    {
        if ($request->user()) {
            $user = $request->user();

            $columns = ['id', 'name', 'instrument'];
            $prospectsToday = Prospect::select($columns)
                ->where('user_id', $user->id)
                ->whereDate('created_at', Carbon::today())
                ->get();

            return response()->json($prospectsToday, 200);
        }

        return response()->json([
            'message' => 'Not logged in',
            'success' => false,
            'status_code' => 500
        ], 500);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function prospectsCount(Request $request) {
        if ($request->user()) {
            $user = $request->user();

            $prospectsCount = Prospect::where('user_id', $user->id)
                ->count();

            return response()->json($prospectsCount, 200);
        }

        return response()->json([
            'message' => 'У вас нет доступа',
            'success' => false,
            'status_code' => 500
        ], 500);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateProfileRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProfileRequest $request, $id)
    {
        $params = $request->all();

        if ($id == Auth::id()) {
            $user = User::find(Auth::id());

            // $request->validate([
            //     'nickname' => [
            //         Rule::unique('users')->ignore($user->id),
            //         'nullable',
            //         'alpha_dash',
            //         'min:3',
            //         'max:30'
            //     ],
            // ]);

            if ($params['avatar'] == null) {
                $params['avatar'] = $user->avatar;
            }

            if ($params['photo_money'] == null) {
                $params['photo_money'] = $user->photo_money;
            }

            if ($params['photo_auto'] == null) {
                $params['photo_auto'] = $user->photo_auto;
            }

            if ($user->update($params)) {
                return response()->json([
                    'user' => $user,
                    'message' => 'Профиль успешно обновлен',
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
        } else {
            return response()->json([
                'message' => 'Действие не доступно',
                'success' => false,
                'status_code' => 500
            ], 500);
        }
    }

    /**
     * Update avatar.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAvatar(Request $request, $id)
    {
        $params = $request->all();

        if ($id == Auth::id()) {
            $user = User::find(Auth::id());

            //> загрузка и сохранение user's avatar в папке images/avatars
            unset($params['avatar']);

            if ($request->has('avatar')) {
                $image = $request->input('avatar');
                if (strpos($image, 'base64')) {
                    list($type, $image) = explode(';', $image);
                    list(, $image) = explode(',', $image);
                    $image = base64_decode($image);

                    $name_image = Str::of($user->name)->slug() . '_'.time();
                    $folder = 'users_avatars/';
                    $fileName = $name_image. '.png';
                    $filePath = $folder . $name_image. '.png';

                    // удаление старого изображения перед добавлением нового
                    Storage::delete($user->avatar);

                    $path = storage_path('app/public/' . $folder);
                    if (!file_exists($path)) {
                        mkdir($path, 0755, true);
                    }
                    file_put_contents($path . $fileName, $image);

                    $params['avatar'] = $filePath;
                } else {
                    $params['avatar'] = $image;
                }
            }

            if ($user->update($params)) {
                return response()->json([
                    'user' => $user,
                    'message' => 'Аватар успешно обновлен',
                    'success' => true,
                    'status_code' => 200
                ], 200);
            } else {
                Storage::delete($user->avatar);
                return response()->json([
                    'message' => 'Ошибка обновления, попробуйте снова',
                    'success' => false,
                    'status_code' => 500
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Действие не доступно',
                'success' => false,
                'status_code' => 500
            ], 500);
        }
    }

    /**
     * Update photo_money.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePhotoMoney(Request $request, $id)
    {
        $params = $request->all();

        if ($id == Auth::id()) {
            $user = User::find(Auth::id());

            //> загрузка и сохранение user's photo_money в папке images/photo_money
            unset($params['photo_money']);

            if ($request->has('photo_money')) {
                // удаление старого изображения перед добавлением нового
                Storage::delete($user->photo_money);

                $image_money = $request->file('photo_money');
                $name_image_money = Str::of($user->name)->slug() . '_money_'.time();
                $folder_money = 'users_money/';
                $filePathMoney = $folder_money . $name_image_money . '.' . $image_money->getClientOriginalExtension();
                $this->uploadOneImage($image_money, $folder_money, 'public', $name_image_money);

                $params['photo_money'] = $filePathMoney;
            }

//            if ($request->has('photo_money')) {
//                $image = $request->input('photo_money');
//                if (strpos($image, 'base64')) {
//                    list($type, $image) = explode(';', $image);
//                    list(, $image) = explode(',', $image);
//                    $image = base64_decode($image);
//
//                    $name_image = Str::of($user->name)->slug() . '_money_'.time();
//                    $folder = 'users_money/';
//                    $fileName = $name_image. '.png';
//                    $filePath = $folder . $name_image. '.png';
//
//                    // удаление старого изображения перед добавлением нового
//                    Storage::delete($user->photo_money);
//
//                    $path = storage_path('app/public/' . $folder);
//                    if (!file_exists($path)) {
//                        mkdir($path, 0755, true);
//                    }
//                    file_put_contents($path . $fileName, $image);
//
//                    $params['photo_money'] = $filePath;
//                } else {
//                    $params['photo_money'] = $image;
//                }
//            }

            if ($user->update($params)) {
                return response()->json([
                    'user' => $user,
                    'message' => 'Фото чека успешно обновлено',
                    'success' => true,
                    'status_code' => 200
                ], 200);
            } else {
                Storage::delete($user->photo_money);
                return response()->json([
                    'message' => 'Ошибка обновления, попробуйте снова',
                    'success' => false,
                    'status_code' => 500
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Действие не доступно',
                'success' => false,
                'status_code' => 500
            ], 500);
        }
    }

    /**
     * Update photo_auto.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePhotoAuto(Request $request, $id)
    {
        $params = $request->all();

        if ($id == Auth::id()) {
            $user = User::find(Auth::id());

            //> загрузка и сохранение user's photo_auto в папке images/photo_auto
            unset($params['photo_auto']);

            if ($request->has('photo_auto')) {
                // удаление старого изображения перед добавлением нового
                Storage::delete($user->photo_auto);

                $image_auto = $request->file('photo_auto');
                $name_image_auto = Str::of($user->name)->slug() . '_auto_'.time();
                $folder_auto = 'users_auto/';
                $filePathAuto = $folder_auto . $name_image_auto . '.' . $image_auto->getClientOriginalExtension();
                $this->uploadOneImage($image_auto, $folder_auto, 'public', $name_image_auto);

                $params['photo_auto'] = $filePathAuto;
            }

//            if ($request->has('photo_auto')) {
//                $image = $request->input('photo_auto');
//                if (strpos($image, 'base64')) {
//                    list($type, $image) = explode(';', $image);
//                    list(, $image) = explode(',', $image);
//                    $image = base64_decode($image);
//
//                    $name_image = Str::of($user->name)->slug() . '_auto_'.time();
//                    $folder = 'users_auto/';
//                    $fileName = $name_image. '.png';
//                    $filePath = $folder . $name_image. '.png';
//
//                    // удаление старого изображения перед добавлением нового
//                    Storage::delete($user->photo_auto);
//
//                    $path = storage_path('app/public/' . $folder);
//                    if (!file_exists($path)) {
//                        mkdir($path, 0755, true);
//                    }
//                    file_put_contents($path . $fileName, $image);
//
//                    $params['photo_auto'] = $filePath;
//                } else {
//                    $params['photo_auto'] = $image;
//                }
//            }

            if ($user->update($params)) {
                return response()->json([
                    'user' => $user,
                    'message' => 'Фото авто успешно обновлено',
                    'success' => true,
                    'status_code' => 200
                ], 200);
            } else {
                Storage::delete($user->photo_auto);
                return response()->json([
                    'message' => 'Ошибка обновления, попробуйте снова',
                    'success' => false,
                    'status_code' => 500
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Действие не доступно',
                'success' => false,
                'status_code' => 500
            ], 500);
        }
    }

    /**
     * @param ChangeUserPasswordRequest $request
     * @param UpdateUserPasswordAction $updateUserPasswordAction
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangeUserPasswordRequest $request, UpdateUserPasswordAction $updateUserPasswordAction, $id)
    {
        if ($id == Auth::id()) {
            $user = User::findOrFail(Auth::id());

            if ($updateUserPasswordAction->run($request->all(), $user)) {
                return response()->json([
                    'success' => true,
                    'user' => $user,
                    'message' => 'Пароль успешно изменен',
                    'status_code' => 200
                ], 200);
            }

            return response()->json([
                'message' => 'Неверный текущий пароль, попробуйте еще раз',
                'success' => false,
                'status_code' => 500
            ], 500);
        } else {
            return response()->json([
                'message' => 'Действие не доступно',
                'success' => false,
                'status_code' => 500
            ], 500);
        }
    }

    public function destroy($id)
    {
        $prospect = Prospect::find($id);

        if ($prospect->delete()) {
            return response()->json([
                'success' => true,
                'prospect' => $prospect,
                'message' => 'Заявка успешно удалена',
                'status_code' => 200
            ], 200);
        } else {
            return response()->json([
                'message' => 'Ошибка удаления, попробуйте снова',
                'success' => false,
                'status_code' => 500
            ], 500);
        }
    }

    public function restore($id)
    {
        $prospect = Prospect::withTrashed()->where('id', $id)->first();

        if ($prospect->restore()) {
            return response()->json([
                'success' => true,
                'prospect' => $prospect,
                'message' => 'Заявка успешно восстановлена',
                'status_code' => 200
            ], 200);
        } else {
            return response()->json([
                'message' => 'Ошибка восстановления, попробуйте снова',
                'status_code' => 500
            ], 500);
        }
    }
}
