<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProfileRequest;
use Illuminate\Http\Request;
use Exception;

class ProfileController extends Controller
{
    public function edit()
    {
        $admin = auth('admin')->user();
        return view('pages.profile.edit', compact('admin'));
    }

    public function update(ProfileRequest $request)
    {

        $admin = auth()->guard('admin')->user();


        try {
            $admin->username = $request->username;
            $admin->email = $request->email;

            if ($request->filled('password')) {
                $admin->password = bcrypt($request->password);
            }

            $admin->save();

            return redirect()->route('profile.edit')->with('success', __('profile_updated_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
}
