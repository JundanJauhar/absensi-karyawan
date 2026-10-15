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
     * Proses autentikasi login (mendukung email maupun username)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Email atau username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $loginInput = trim($request->input('email'));
        $password   = $request->input('password');
        $remember   = $request->boolean('remember');

        $attemptSuccess = false;

        // Cek jika input adalah format email
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $attemptSuccess = Auth::attempt(['email' => $loginInput, 'password' => $password], $remember);
        } else {
            // Cek jika input adalah username / nama
            $attemptSuccess = Auth::attempt(['name' => $loginInput, 'password' => $password], $remember);

            // Jika input 'admin', fallback cek ke email admin@admin.com atau admin@company.com
            if (!$attemptSuccess && strtolower($loginInput) === 'admin') {
                $attemptSuccess = Auth::attempt(['email' => 'admin@admin.com', 'password' => $password], $remember)
                               || Auth::attempt(['email' => 'admin@company.com', 'password' => $password], $remember);
            }
        }

        if ($attemptSuccess) {
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
                     ->withErrors(['email' => 'Email/Username atau kata sandi yang Anda masukkan tidak cocok.']);
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