<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    {{-- You can link to a CSS file here for styling --}}
</head>
<body>
<h1>Create an Account</h1>

{{-- This will display any validation errors --}}
@if ($errors->any())
    <div style="color: red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    {{-- Name --}}
    <div>
        <label for="name">Name</label><br>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
    </div>

    {{-- Email Address --}}
    <div style="margin-top: 1rem;">
        <label for="email">Email</label><br>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required>
    </div>

    {{-- Password --}}
    <div style="margin-top: 1rem;">
        <label for="password">Password</label><br>
        <input id="password" type="password" name="password" required>
    </div>

    {{-- Confirm Password --}}
    <div style="margin-top: 1rem;">
        <label for="password_confirmation">Confirm Password</label><br>
        <input id="password_confirmation" type="password" name="password_confirmation" required>
    </div>

    <div style="margin-top: 1rem;">
        <button type="submit">
            Register
        </button>
    </div>
</form>
</body>
</html>
