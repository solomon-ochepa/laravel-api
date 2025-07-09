<?php

namespace Modules\Role\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Modules\Role\App\Models\Role;

class UpdateRoleRequest extends FormRequest
{
    public Role $role;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:16', 'unique:roles,name,'.$this->role?->id.',name'],
            'guard_name' => ['nullable', 'string', 'in:web,api]'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->can('roles.update');
    }
}
