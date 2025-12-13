<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\LoginLog;

class AuthController extends Controller
{
    // 1. Tampilkan Halaman Login
    public function showLoginForm(Request $request)
    {
        // Log Access
        LoginLog::create([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Get Latest Logs
        $logs = LoginLog::latest()->take(10)->get();

        return view('auth.login', compact('logs'));
    }

    // 2. Proses Login
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Coba Login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect sesuai Role
            $role = Auth::user()->role;
            
            if ($role === 'admin') {
                return redirect()->intended('dashboard/admin');
            } elseif ($role === 'area_manager') {
                return redirect()->intended('dashboard/area'); // Dashboard Area Manager
            } elseif ($role === 'store_manager') {
                return redirect()->intended('dashboard/store'); // Dashboard Kepala Toko
            } elseif ($role === 'hr') {
                return redirect()->intended('dashboard/hr'); // Dashboard HR
            }

            return redirect()->intended('/');
        }

        // Jika Gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // 3. Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}