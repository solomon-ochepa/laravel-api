<?php

namespace Modules\User\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Modules\User\App\Models\User;

class UpdateUserRequest extends FormRequest
{
    public User $user;

    public function __construct()
    {
        $this->user = (request('user') instanceof (new User)) ? request('user') : User::find(request('user'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:32'],
            'last_name' => ['sometimes', 'required', 'string', 'max:32'],
            'other_name' => ['sometimes', 'nullable', 'string', 'max:32'],
            'username' => ['sometimes', 'nullable', 'string', "unique:users,username,{$this->user->id}"],
            'phone' => ['sometimes', 'required', 'string', "unique:users,phone,{$this->user->id}"],
            'email' => ['sometimes', 'nullable', 'email', "unique:users,email,{$this->user->id}"],
            'password' => ['sometimes', 'required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // TODO: return Auth::user()->can('users.update');
    }
}
