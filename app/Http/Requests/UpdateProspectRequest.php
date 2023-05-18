<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProspectRequest extends FormRequest
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
            'name' => 'nullable|string|min:2|max:100',
            'city' => 'nullable|string|min:2|max:30',
            'phone' => 'nullable|numeric',
            'phone_whatsapp' => 'nullable|numeric',
            'phone_viber' => 'nullable|numeric',
            'telegram' => 'nullable|string',
            'email' => 'nullable|string|email|max:191',
            'user_id' => 'required|numeric',
            'instrument' => 'nullable|string|min:3|max:30',
            'action_bot' => 'nullable|string|min:3|max:30',
            'test_result' => 'nullable|string|min:10|max:191',
            'step' => 'required|string',
            'result' => 'nullable|string',
            'comment' =>'nullable',
        ];
    }
}
