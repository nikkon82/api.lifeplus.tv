<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:2|max:30',
            'role' => 'required|alpha',
            'last_name' => 'nullable|min:3|max:30',
            'country' => 'nullable|string|min:2|max:30',
            'city' => 'nullable|string|min:2|max:30',
            'gender' => 'nullable|string',
			'who_is' => 'nullable|string|max:191',
            'phone' => 'nullable|numeric',
            'phone_whatsapp' => 'nullable|numeric',
            'phone_viber' => 'nullable|numeric',
            'telegram' => 'nullable|string|min:13|max:50',
            'instagram' => 'nullable',
            'vkontakte' => 'nullable',
            'odnoklassniki' => 'nullable',
            'about_me' => 'nullable',
            'about_me_viz' => 'nullable',
            'about_me_biz' => 'nullable',
            'dop_viz' => 'nullable',
            'viz_design' => 'required|string|max:191',
            'biz_video_title' => 'nullable|string|max:191',
            'biz_video_link' => 'nullable|string|max:191',
            'biz_test_dop' => 'nullable',
            'about_chat' => 'nullable',
            'fb_messenger' => 'nullable|string|min:2|max:50',
            'email_verified_at' => 'required|date'
        ];
    }
}
