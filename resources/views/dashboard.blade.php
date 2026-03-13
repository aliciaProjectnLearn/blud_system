<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>

    <h2>Dashboard</h2>

    {{-- Alert setelah login berhasil --}}
    @if (session('success'))
        <div style="color:green;">{{ session('success') }}</div>
    @endif

    <p>Selamat datang, {{ Auth::user()->name }}!</p>

    {{-- Menambahkan form/button logout --}}
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>

</body>
</html>
