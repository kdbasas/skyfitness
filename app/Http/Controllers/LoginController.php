<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login.login'); // Adjust path if necessary
    }

    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|string',
        'password' => 'required|string',
    ]);

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } 
        //elseif ($user->role === 'staff') {
           // return redirect()->route('staff.dashboard');
        //}

        return redirect()->route('login')->withErrors(['role' => 'Unauthorized']);
    }

    return redirect()->route('login')->withErrors(['email' => 'Invalid credentials']);
}

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
