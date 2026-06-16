<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sistem SPK Digitalisasi Sekolah</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            /* Style bawaan tetap aman di sini */
        </style>
    @endif
</head>

<body
    class="bg-slate-50 dark:bg-zinc-950 text-slate-900 flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">

    <!-- HEADER -->
    <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden">
        @if (Route::has('login'))
            <nav class="flex items-center justify-end gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="inline-block px-5 py-1.5 dark:text-zinc-200 border-slate-200 dark:border-zinc-800 hover:border-slate-300 dark:hover:border-zinc-700 border bg-white dark:bg-zinc-900 rounded-md text-sm font-medium shadow-sm transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-block px-5 py-1.5 dark:text-zinc-300 text-slate-600 hover:text-slate-900 dark:hover:text-white rounded-md text-sm font-medium transition">
                        Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="inline-block px-5 py-1.5 text-white bg-blue-600 hover:bg-blue-700 rounded-md text-sm font-medium shadow-sm transition">
                            Register
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    <!-- MAIN CARD -->
    <div
        class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
        <main
            class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row bg-white dark:bg-zinc-900 shadow-md rounded-2xl overflow-hidden border border-slate-100 dark:border-zinc-800/80">

            <!-- KIRI: JUDUL & TOMBOL -->
            <div class="flex-1 p-8 lg:p-16 flex flex-col justify-center">
                <span
                    class="inline-flex items-center gap-1.5 bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 text-xs font-semibold px-3 py-1 rounded-full w-fit mb-4">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                    Sistem Pendukung Keputusan
                </span>

                <h1
                    class="text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mb-3 tracking-tight leading-tight">
                    Analisis & Pemantauan <br class="hidden lg:block">Digitalisasi Sekolah
                </h1>

                <p class="text-sm text-slate-500 dark:text-zinc-400 mb-8 leading-relaxed max-w-md">
                    Panel administrasi pusat untuk evaluasi keaktifan platform dan manajemen subdomain sekolah
                    menggunakan algoritma <b>Simple Additive Weighting (SAW)</b>.
                </p>

                <div>
                    <a href="{{ route('login') }}"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-semibold shadow-md shadow-blue-500/10 hover:shadow-blue-500/20 transition-all">
                        Masuk ke Panel Admin &rarr;
                    </a>
                </div>
            </div>

            <!-- KANAN: EMBED GRADASI BERWARNA (MODERN & ELEGAN) -->
            <div
                class="bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 p-8 lg:p-12 flex flex-col justify-between w-full lg:w-[340px] shrink-0 text-white relative overflow-hidden">

                <!-- Aksen Dekoratif Abstrak Bulat Tipis -->
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-2xl pointer-events-none">
                </div>
                <div
                    class="absolute -bottom-10 -left-10 w-40 h-40 bg-purple-500/20 rounded-full blur-2xl pointer-events-none">
                </div>

                <div class="my-auto text-center lg:text-left relative z-10">
                    <div
                        class="text-[11px] font-bold uppercase tracking-widest text-blue-200 mb-1.5 bg-white/10 px-2.5 py-0.5 rounded-full w-fit mx-auto lg:mx-0">
                        SAW Engine v1.0
                    </div>
                    <div class="text-xl font-bold tracking-tight text-white">
                        Dashboard Pusat
                    </div>
                    <p class="text-xs text-blue-100/80 mt-1">
                        Platform Evaluasi Infrastruktur Web
                    </p>
                </div>

                <div
                    class="text-[11px] text-blue-200/60 mt-6 text-center lg:text-left relative z-10 border-t border-white/10 pt-4">
                    &copy; {{ date('Y') }} — Panel Manajemen Berbasis Framework Keamanan Data.
                </div>
            </div>

        </main>
    </div>

    @if (Route::has('login'))
        <div class="h-14.5 hidden lg:block"></div>
    @endif
</body>

</html>