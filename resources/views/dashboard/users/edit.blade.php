@extends('layouts.app')

@section('content')

<div class="container-fluid">

<h1 class="h3 mb-4 text-gray-800">Edit User</h1>

<div class="card shadow">

<div class="card-body">

<form action="{{ route('users.update', $user->id) }}" method="POST">

@csrf
@method('PUT')

<div class="form-group">
<label>Nama</label>
<input type="text" name="name" class="form-control" value="{{ $user->name }}">
</div>

<div class="form-group">
<label>Email</label>
<input type="email" name="email" class="form-control" value="{{ $user->email }}">
</div>

<div class="form-group">
<label>Password (kosongkan jika tidak diubah)</label>
<input type="password" name="password" class="form-control">
</div>

<div class="form-group">
<label>Role</label>

<select name="role" class="form-control">

<option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>
Admin
</option>

<option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>
User
</option>

</select>

</div>

<button class="btn btn-primary mt-3">
Update
</button>

<a href="{{ route('users.index') }}" class="btn btn-secondary mt-3">
Kembali
</a>

</form>

</div>
</div>

</div>

@endsection