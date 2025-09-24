<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Docs - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <div class="max-w-5xl mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Documentation</h1>
        <p class="text-gray-600 mb-8">Developer and user docs will live here.</p>
        <a class="text-blue-600 underline" href="{{ route('home') }}">Back to home</a>
    </div>
</body>
</html>


