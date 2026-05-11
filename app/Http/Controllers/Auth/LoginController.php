<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLoginForm()
    {
        // Redirect jika sudah login
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Proses percobaan login.
     */
    public function login(Request $request)
    {
        // 1. Validasi input: username dan password
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // 2. Lakukan otentikasi dengan menggunakan 'username'
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Otentikasi berhasil
            $request->session()->regenerate();

            // Opsional: Redirect berdasarkan role
            return $this->redirectToRoleBasedDashboard(Auth::user()->role);

            // Default redirect
            // return redirect()->intended('/dashboard');
        }

        // Otentikasi gagal
        return back()->withErrors([
            'username' => 'Username atau password salah. Silakan coba lagi.',
        ])->onlyInput('username');
    }

    /**
     * Logika redirect berdasarkan role.
     */
    protected function redirectToRoleBasedDashboard(string $role)
    {
        switch ($role) {
            case 'administrator-sistem':
                return redirect('admin/dashboard');
            case 'operator-bumdes':
                return redirect('bumdes/dashboard');
            case 'verifikator':
                return redirect('admin/dashboard');
            case 'kepala-bidang':
                return redirect('admin/dashboard');
            case 'kepala-dinas':
                return redirect('admin/dashboard');
            case 'administrator-opd':
                return redirect('admin/dashboard');
            default:
                return redirect('admin/dashboard');
        }
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout(); // Hapus session autentikasi

        $request->session()->invalidate(); // Hapus data session
        $request->session()->regenerateToken(); // Buat ulang CSRF token

        return redirect('/'); // Kembali ke halaman login
    }
}
