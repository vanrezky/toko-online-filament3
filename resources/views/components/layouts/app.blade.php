<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ settings('favicon') }}">

    <title>{{ !empty($title) ? $title . ' - ' . settings('site_name') : settings('site_name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Glory:ital,wght@0,500;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.css">

    @stack('styles')

    @vite('resources/css/app.css')
    @livewireStyles
</head>

<body class="flex flex-col min-h-screen">
    @livewire('partials.navbar')
    <main class="flex-grow">
        {{ $slot }}
    </main>
    <x-toaster-hub />
    @livewire('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.js"></script>
    @stack('scripts')
    @vite('resources/js/app.js')
    @livewireScripts
</body>

</html>
