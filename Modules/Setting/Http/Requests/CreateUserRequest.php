<?php

namespace Modules\Setting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => [
                'required',
                Rule::unique('users', 'email')->where(function ($query) {
                    return $query->where('cabang_id', $this->cabang_id);
                }),
            ],
            'username' => [
                'required',
                Rule::unique('users', 'username')->where(function ($query) {
                    return $query->where('cabang_id', $this->cabang_id);
                }),
            ],
            'password' => 'required|confirmed',
            'cabang_id' => 'required',
            'roles_id' => 'required',
            'nohp_profile' => 'required',
            'jeniskelamin_profile' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email harus unik',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username harus unik',
            'cabang_id.required' => 'Cabang wajib diisi',
            'roles_id.required' => 'Roles wajib diisi',
            'nohp_profile.required' => 'No. handphone wajib diisi',
            'jeniskelamin_profile.required' => 'Jenis kelamin wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.confirmed' => 'Konfirmasi password wajib diisi',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
