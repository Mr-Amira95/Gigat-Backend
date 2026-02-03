<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Ensure only logged-in admins can update their own profile
        return auth()->guard('admin')->check();
    }

    public function rules(): array
    {
        $adminId = auth()->guard('admin')->id();

        return [
            'username' => 'required|string|max:255',
            'email'    => 'required|email|unique:admins,email,' . $adminId,
            'password' => 'nullable|min:8|confirmed',
        ];
    }
}
