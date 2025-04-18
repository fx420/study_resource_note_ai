<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile', ['user' => auth()->user()]);
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'gender'   => 'required|in:Male,Female,Other',
            'age'      => 'required|integer|min:1|max:150',
            'course'   => 'required|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->username = $request->username;
        $user->email = $request->email;
        $user->gender = $request->gender;
        $user->age = $request->age;
        $user->course = $request->course;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('profile.index')->with('success', 'Profile updated successfully.');
    }
}
