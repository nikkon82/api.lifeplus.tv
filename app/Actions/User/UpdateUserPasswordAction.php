<?php

namespace App\Actions\User;

use Illuminate\Support\Facades\Hash;

class UpdateUserPasswordAction
{
    public function run($request, $user)
    {
        if (!Hash::check($request['oldPassword'], $user->password)) {
            return false;
        }

        $user->password = Hash::make($request['password']);

        if (!$user->save()) {
            return false;
        }

        return true;
    }
}
