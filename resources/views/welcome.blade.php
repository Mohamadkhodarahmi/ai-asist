<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to AI Assistant</title>
    {{-- You can add your main CSS file here --}}
</head>
<body style="font-family: sans-serif; text-align: center; padding-top: 5rem;">

<div class="main-content">
    <h1 style="font-size: 2.5rem;">Welcome to Your Custom AI Assistant</h1>
    <p style="color: #4b5563; font-size: 1.25rem;">Upload your business documents and get instant, accurate answers.</p>
    <div style="margin-top: 2rem;">
        <a href="{{ route('login') }}" style="text-decoration: none; background-color: #3b82f6; color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; margin-right: 1rem;">Log In</a>
        <a href="{{ route('register') }}" style="text-decoration: none; background-color: #e5e7eb; color: #1f2937; padding: 0.75rem 1.5rem; border-radius: 0.5rem;">Register</a>
    </div>
</div>

</body>
</html>
