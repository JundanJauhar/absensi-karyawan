<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('karyawan.index')
                : redirect()->route('portal.index');
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->intended(route('karyawan.index'))
                                 ->with('success', "Selamat datang kembali, {$user->name} (Admin HR).");
            }

            return redirect()->intended(route('portal.index'))
                             ->with('success', "Selamat datang, {$user->name}. Selamat bekerja!");
        }

        return back()->withInput($request->only('email'))
                     ->withErrors(['email' => 'Email atau kata sandi yang Anda masukkan tidak cocok.']);
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}