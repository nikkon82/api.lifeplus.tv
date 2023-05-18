<?php

namespace App\Http\Controllers\Api;

use App\Actions\User\UpdateUserPasswordAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeUserPasswordRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdatePackageRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Package;
use App\Models\User;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use UploadImageTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $columns = ['id', 'name', 'last_name', 'lr_number', 'role', 'deleted_at'];
        $users = User::withTrashed()->select($columns)->get();

        return response()->json($users, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateUserRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateUserRequest $request)
    {
        $params = $request->all();

        $params['password'] = Hash::make($params['password']);

        $user = User::create($params);

        if ($user->exists) {
            return response()->json([
                'user' => $user,
                'message' => 'Пользователь успешно добавлен',
                'success' => true,
                'status_code' => 200
            ], 200);
        } else {
            return response()->json([
                'message' => 'Ошибка создания, попробуйте еще раз',
                'success' => false,
                'status_code' => 500
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::find($id);

        return response()->json($user, 200);
    }

    /**
     * all user instruments
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function userPackage($id)
    {
        $userPackage = Package::where('user_id', $id)->get();

        return response()->json($userPackage, 200);
    }

    /**
     * user instrument access
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function userAccess($id)
    {
        $userAccess = Package::where('user_id', $id)->first();

        return response()->json($userAccess, 200);
    }

    /**
     * update user instrument
     *
     * @param UpdatePackageRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserAccess(UpdatePackageRequest $request, $id)
    {
        $params = $request->all();

        $user = Package::where('user_id', $id)->first();

        if ($user) {
            $result = $user->update($params);
        } else {
            $user = Package::create($params);

            $result = $user;
        }

        if ($result) {
            return response()->json([
                'user' => $user,
                'message' => 'Успешно',
                'success' => true,
                'status_code' => 200
            ], 200);
        } else {
            return response()->json([
                'message' => 'Ошибка, попробуйте снова',
                'success' => false,
                'status_code' => 500
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateUserRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $params = $request->all();

        $user = User::find($id);

        $request->validate([
            'email' => [
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $request->validate([
            'lr_number' => [
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $params['email'] = $request['email'];

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
                'message' => 'Пользователь успешно обновлен',
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
     * Update avatar.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAvatar(Request $request, $id)
    {
        $params = $request->all();

        $user = User::find($id);

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
    }

    /**
     * Update photo_money.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePhotoMoney(Request $request, $id)
    {
        $params = $request->all();

        $user = User::find($id);

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

//        if ($request->has('photo_money')) {
//            $image = $request->input('photo_money');
//            if (strpos($image, 'base64')) {
//                list($type, $image) = explode(';', $image);
//                list(, $image) = explode(',', $image);
//                $image = base64_decode($image);
//
//                $name_image = Str::of($user->name)->slug() . '_money_'.time();
//                $folder = 'users_money/';
//                $fileName = $name_image. '.png';
//                $filePath = $folder . $name_image. '.png';
//
//                // удаление старого изображения перед добавлением нового
//                Storage::delete($user->photo_money);
//
//                $path = storage_path('app/public/' . $folder);
//                if (!file_exists($path)) {
//                    mkdir($path, 0755, true);
//                }
//                file_put_contents($path . $fileName, $image);
//
//                $params['photo_money'] = $filePath;
//            } else {
//                $params['photo_money'] = $image;
//            }
//        }

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
    }

    /**
     * Update photo_auto.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePhotoAuto(Request $request, $id)
    {
        $params = $request->all();

        $user = User::find($id);

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

//        if ($request->has('photo_auto')) {
//            $image = $request->input('photo_auto');
//            if (strpos($image, 'base64')) {
//                list($type, $image) = explode(';', $image);
//                list(, $image) = explode(',', $image);
//                $image = base64_decode($image);
//
//                $name_image = Str::of($user->name)->slug() . '_auto_'.time();
//                $folder = 'users_auto/';
//                $fileName = $name_image. '.png';
//                $filePath = $folder . $name_image. '.png';
//
//                // удаление старого изображения перед добавлением нового
//                Storage::delete($user->photo_auto);
//
//                $path = storage_path('app/public/' . $folder);
//                if (!file_exists($path)) {
//                    mkdir($path, 0755, true);
//                }
//                file_put_contents($path . $fileName, $image);
//
//                $params['photo_auto'] = $filePath;
//            } else {
//                $params['photo_auto'] = $image;
//            }
//        }

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
    }

    public function changePassword(ChangeUserPasswordRequest $request, UpdateUserPasswordAction $updateUserPasswordAction, $id)
    {
        $user = User::findOrFail($id);

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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if ($user->delete()) {
            return response()->json([
                'success' => true,
                'user' => $user,
                'message' => 'Пользователь успешно удален',
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

    /**
     * Restore deleting user
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $user = User::withTrashed()->where('id', $id)->first();

        if ($user->restore()) {
            return response()->json([
                'success' => true,
                'user' => $user,
                'message' => 'Пользователь успешно восстановлен',
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
