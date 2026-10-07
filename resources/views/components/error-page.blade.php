@props(['code', 'title', 'message'])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 font-sans antialiased">
    <div class="flex min-h-screen items-center justify-center px-6">
        <div class="w-full max-w-md rounded-2xl bg-white p-8 text-center shadow-sm">
            <p class="text-base font-semibold text-gray-600">Error {{ $code }}</p>
            <h1 class="mt-3 text-3xl font-bold text-[#12263F]">{{ $title }}</h1>
            <p class="mt-4 text-lg text-gray-700">{{ $message }}</p>
            <a href="{{ url('/') }}" class="mt-8 block rounded-2xl bg-blue-700 py-4 text-xl font-bold text-white">Go to the home page</a>
        </div>
    </div>
</body>
</html>