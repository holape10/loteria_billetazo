<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gana Dinero con El Billetazo</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-950">
    <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0 sm:justify-center">
        <div class="mb-6 text-2xl font-extrabold text-dorado-400">
            💰 El Billetazo
        </div>

        <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-md overflow-hidden sm:rounded-2xl border-t-4 border-dorado-500">
            {{ $slot }}
        </div>
    </div>
</body>
</html>