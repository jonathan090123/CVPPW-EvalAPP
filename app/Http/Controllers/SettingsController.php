<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('settings.edit');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ], [
            'current_password.current_password' => 'Password saat ini salah.',
        ]);

        $user = $request->user();
        $user->password = $validated['password'];
        $user->save();

        return redirect()->route('settings.edit')
            ->with('success', 'Password berhasil diperbarui.');
    }
}
