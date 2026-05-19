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
            // Generate session dari field user
            $user = Auth::user();
            $request->session()->put([
                'user_id'    => $user->id,
                'user_name'  => $user->name,
                'user_email' => $user->email,
                'user_role'  => $user->role,
                'username'   => $user->username,
                'bumdes_id'  => $user->bumdes_id,
                'opd_id'     => $user->opd_id,
            ]);

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
                return redirect('dashboard');
            case 'operator-bumdes':
                return redirect('dashboard');
            case 'verifikator':
                return redirect('dashboard');
            case 'approver':
                return redirect('dashboard');

            case 'operator-opd':
                return redirect('dashboard');
            default:
                return redirect('dashboard');
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
