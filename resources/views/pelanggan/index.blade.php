@extends('layouts.app')

@section('content')

<div style="padding:40px">
    <h1>Dashboard Pelanggan</h1>
    <p>Selamat datang, {{ auth()->user()->nama_lengkap }}</p>
</div>

@endsection
