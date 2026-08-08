<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => 'required|string|max:100',
            'password' => 'required|string',
        ]);

        if (auth()->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('assessments.index'));
        }

        return back()
            ->withInput($request->only('username'))
            ->withErrors(['username' => 'Username atau password salah.']);
    }

    public function showRegister(): View
    {
        $positions = [
            'KEPALA BAGIAN' => 'Kepala Bagian',
            'KOORDINATOR' => 'Koordinator',
            'STAFF' => 'Staff',
        ];

        return view('auth.register', compact('positions'));
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|max:100|unique:users,username',
            'position' => 'required|in:KEPALA BAGIAN,KOORDINATOR,STAFF',
            'password' => 'required|string|min:4|confirmed',
        ]);

        User::create([
            'username' => $validated['username'],
            'position' => $validated['position'],
            'password' => $validated['password'],
        ]);

        return redirect()->route('login')
            ->with('success', 'Akun berhasil dibuat. Silakan login.');
    }

    public function logout(Request $request): RedirectResponse
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}