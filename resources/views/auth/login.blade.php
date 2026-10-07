<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'FreightPath') }} - Sign in</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
<div class="min-h-screen flex">

    <div class="hidden lg:flex lg:w-1/2 bg-[#12263F] text-white flex-col justify-between p-16">
        <x-brand-logo :light="true" />

        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-300">Connected fleets. Clearer roads.</p>
            <h1 class="mt-6 text-6xl font-bold leading-tight tracking-tight">Every mile.<br>Under control.</h1>
            <p class="mt-6 max-w-md text-lg leading-relaxed text-blue-200">
                A better way to keep your drivers, trips, and operations moving together.
            </p>

            <div class="mt-16 flex items-center gap-4 text-blue-300">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-7 w-7 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 21s-6-5.2-6-10a6 6 0 1 1 12 0c0 4.8-6 10-6 10z"/><circle cx="12" cy="11" r="2"/>
                </svg>
                <div class="flex-1 border-t-2 border-dashed border-blue-300/40"></div>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-10 w-10 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 6h11v10H3z"/><path d="M14 10h4l3 3v3h-7z"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/>
                </svg>
                <div class="flex-1 border-t-2 border-dashed border-blue-300/40"></div>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-7 w-7 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 21s-6-5.2-6-10a6 6 0 1 1 12 0c0 4.8-6 10-6 10z"/><circle cx="12" cy="11" r="2"/>
                </svg>
            </div>
        </div>

        <p class="text-sm text-blue-300/80">&copy; {{ date('Y') }} FreightPath. Built to keep you moving.</p>
    </div>

    <div class="flex w-full lg:w-1/2 items-center justify-center bg-white px-6 py-12">
        <div class="w-full max-w-md">
            <x-brand-logo />

            <p class="mt-14 text-xs font-semibold uppercase tracking-widest text-gray-400">Your operations, connected</p>
            <h2 class="mt-4 text-4xl font-bold tracking-tight text-[#12263F]">Welcome back</h2>
            <p class="mt-3 text-gray-500">Sign in to your FreightPath account.</p>

            <x-auth-session-status class="mt-6" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="mt-10 space-y-6">
                @csrf

                <div class="grid grid-cols-2 gap-1 rounded-xl bg-gray-100 p-1">
                    <label class="cursor-pointer">
                        <input type="radio" name="login_as" value="dispatcher" class="peer sr-only"
                        {{ old('login_as', 'dispatcher') === 'dispatcher' ? 'checked' : '' }}>
                        <span class="flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-gray-500 transition peer-checked:bg-white peer-checked:text-blue-600 peer-checked:shadow-sm peer-focus-visible:ring-2 peer-focus-visible:ring-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="4" y="4" width="7" height="7" rx="1"/><rect x="13" y="4" width="7" height="7" rx="1"/>
                                <rect x="4" y="13" width="7" height="7" rx="1"/><rect x="13" y="13" width="7" height="7" rx="1"/>
                            </svg>
                            Dispatcher
                        </span>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="login_as" value="driver" class="peer sr-only"
                        {{ old('login_as') === 'driver' ? 'checked' : '' }}>
                        <span class="flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-gray-500 transition peer-checked:bg-white peer-checked:text-blue-600 peer-checked:shadow-sm peer-focus-visible:ring-2 peer-focus-visible:ring-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 6h11v10H3z"/><path d="M14 10h4l3 3v3h-7z"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/>
                            </svg>
                            Driver
                         </span>
                    </label>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        placeholder="Enter your email"
                        class="mt-2 block w-full rounded-lg border border-gray-200 px-4 py-3 text-sm placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div x-data="{ show: false }">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="relative mt-2">
                        <input id="password" name="password" :type="show ? 'text' : 'password'" type="password" required autocomplete="current-password"
                        placeholder="Enter your password"
                        class="block w-full rounded-lg border border-gray-200 px-4 py-3 pr-20 text-base placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                        <button type="button" @click="show = ! show" x-text="show ? 'Hide' : 'Show'"
                        class="absolute inset-y-0 right-0 px-4 text-sm font-semibold text-blue-700">Show</button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                
                <label class="flex items-center gap-3 text-base text-gray-700">
                    <input type="checkbox" name="remember" checked class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    Keep me signed in on this device
                </label>

                <button type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-3.5 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Sign in
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14M13 6l6 6-6 6"/>
                    </svg>
                </button>
            </form>

            @if (Route::has('register'))
            <p class="mt-6 text-center text-sm text-gray-500">
                No account? <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:underline">Register</a>
            </p>
            @endif
            
            <p class="mt-10 text-center text-xs text-gray-400">Secure access. Smarter operations.</p>
        </div>
    </div>
</div>
</body>
</html>