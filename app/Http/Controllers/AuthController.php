<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);
    
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }
    
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ]);
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
    
    public function showRegister()
    {
        return view('auth.register');
    }
    
    public function register(Request $request)
    {
        $validated = $request->validate([
            'username'              => ['required','string','max:255','unique:users'],
            'name'                  => ['required','string','max:255'],
            'email'                 => ['required','email','max:255','unique:users'],
            'password'              => ['required','confirmed','min:6'],
        ]);
    
        $user = User::create([
            'username' => $validated['username'],
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);
    
        Auth::login($user);
        return redirect('/');
    }
}
