<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
<h1>Welcome to your Dashboard, {{ Auth::user()->name }}!</h1>

<p>This is your protected dashboard area.</p>

{{-- Logout Button --}}
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Log Out</button>
</form>

<hr style="margin-top: 2rem; margin-bottom: 2rem;">

{{-- Link to the chat interface --}}
<a href="/chat">Go to AI Chat Assistant</a>

</body>
</html>
