<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'], // Bisa disamakan dengan nama_lengkap
            'username'     => ['required', 'string', 'max:50', 'unique:'.User::class],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'no_hp'        => ['required', 'string', 'max:20'],
            'nik'          => ['nullable', 'string', 'max:20', 'unique:'.User::class],
            'email'        => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'          => $request->name,
            'username'      => $request->username,
            'nama_lengkap'  => $request->nama_lengkap,
            'no_hp'         => $request->no_hp,
            'nik'           => $request->nik,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'status_futsal' => 'active', // Default role pelengkap aktif
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
