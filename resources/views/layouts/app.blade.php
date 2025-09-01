    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>AI Assistant</title>
        {{-- You can add your main CSS file here --}}
        @livewireStyles
    </head>
    <body style="font-family: sans-serif; margin: 0; background-color: #f9fafb;">

    {{-- Include the navigation menu --}}
    @include('layouts.navigation')

    {{-- Page Content --}}
    <main>
        @yield('content')
    </main>

    @livewireScripts
    </body>
    </html>
