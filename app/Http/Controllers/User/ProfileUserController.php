<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileUserController extends Controller
{
    /**
     * Menampilkan halaman profil user.
     */
    public function index()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    /**
     * Memperbarui data profil user.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'nama_lengkap'  => ['nullable', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_hp'         => ['nullable', 'string', 'max:20'],
            'alamat'        => ['nullable', 'string', 'max:1000'],
            'username'      => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'      => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama panggilan wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
            'username.unique' => 'Username sudah digunakan oleh pengguna lain.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $data = [
            'name'          => $request->name,
            'nama_lengkap'  => $request->nama_lengkap,
            'email'         => $request->email,
            'no_hp'         => $request->no_hp,
            'alamat'        => $request->alamat,
            'username'      => $request->username,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('user.profile.index')->with('success', 'Profil berhasil diperbarui!');
    }
}
