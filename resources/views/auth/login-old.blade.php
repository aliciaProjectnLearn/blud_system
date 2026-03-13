<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>

    <h2>Login</h2>

    {{-- Menampilkan validasi error message --}}
    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li style="color:red;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Menampilkan alert jika login gagal --}}
    @if (session('error'))
        <div style="color:red;">
                {{ session('error') }}
        </div>
    @endif

    {{-- Redirect ke dashboard setelah login dengan alert --}}
    @if (session('success'))
        <div style="color:green;">
                {{ session('success') }}
        </div>
    @endif

    {{-- Membuat form login sederhana --}}
    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Input field email dan username --}}
        <label>Email:</label><br>
        <input type="email" name="email" value="{{ old('email') }}" required><br><br>

        {{-- Input field password --}}
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        {{-- Button Login --}}
        <button type="submit">Login</button>
    </form>

</body>
</html>
