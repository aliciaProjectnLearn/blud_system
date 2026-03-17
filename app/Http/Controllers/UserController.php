<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
 
class UserController extends Controller
{
    public function index()
    {
        $search = request('search');

        $users = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->where('name','like','Admin%');
            })
            ->when($search, function($query) use ($search){
                $query->where('name','like',"%$search%");
            })
            ->paginate(10);
        return view('dashboard.users.index', compact('users', 'search'));
    }

    public function indexPelanggan()
    {
        $search = request('search');

        $users = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->where('name','like','Pelanggan%');
            })
            ->when($search, function($query) use ($search){
                $query->where('name','like',"%$search%");
            })
            ->paginate(10);
        return view('dashboard.users.pelanggan', compact('users', 'search'));
    }

    public function create()
    {
        $roles = ['Adminac', 'Adminfutsal', 'Adminkantin', 'Superadmin'];
        return view('dashboard.users.create', compact('roles'));
    }

    public function store(Request $request) 
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return redirect()->route('users.index')
            ->with('success','User berhasil ditambahkan');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('dashboard.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'name' => 'required',
        'email' => 'required|email'
    ]);

    $data = [
        'name' => $request->name,
        'email' => $request->email,
        'role' => $request->role
    ];

    if ($request->password) {
        $data['password'] = Hash::make($request->password);
    }

    $user->update($data);

    return redirect()->route('users.index')
        ->with('success', 'User berhasil diupdate');
}

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('dashboard.users.show', compact('user'));
    }
}
