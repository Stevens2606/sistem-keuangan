<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Sistem Keuangan') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex transition-colors duration-300">

    {{-- Sidebar --}}
    <aside class="w-64 bg-white dark:bg-gray-800 shadow-md min-h-screen flex flex-col fixed top-0 left-0 z-10">

        {{-- Logo --}}
        <div class="px-6 py-5 border-b border-gray-100">
            <h1 class="text-lg font-bold text-blue-700">💰 Sistem Keuangan</h1>
            <p class="text-xs text-gray-400 mt-0.5">Manajemen Kas</p>
        </div>

        {{-- Menu --}}
        <nav class="flex-1 px-4 py-4 space-y-1">

            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('transaksi.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('transaksi.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Transaksi
            </a>

            <a href="{{ route('kategori.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('kategori.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Kategori
            </a>

            <a href="{{ route('anggaran.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('anggaran.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Anggaran
            </a>

            <a href="{{ route('laporan.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('laporan.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Laporan PDF
            </a>
            @if(Auth::user()->isAdmin())
            <a href="{{ route('users.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Manajemen User
            </a>

            @if(Auth::user()->isAdmin())
            <a href="{{ route('backup.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('backup.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7M4 7c0-2 1-3 3-3h10c2 0 3 1 3 3M4 7h16M12 11v6M9 14l3 3 3-3"/>
                </svg>
                Backup & Restore
            </a>

           <a href="{{ route('activity-log.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('activity-log.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Log Aktivitas
            </a>
            @endif

            @endif

        </nav>

        {{-- User Info & Logout --}}
        <div class="px-4 py-4 border-t border-gray-100">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 mb-3 hover:bg-gray-50 rounded-lg p-1 transition">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-bold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800 leading-tight">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400">Lihat profil</p>
                </div>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-red-600 hover:bg-red-50 transition font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>

    </aside>

    {{-- Konten Utama --}}
    <div class="flex-1 ml-64 flex flex-col min-h-screen">

        {{-- Topbar --}}
            <header class="bg-white dark:bg-gray-800 shadow-sm px-6 py-4 flex justify-between items-center sticky top-0 z-10">            <div>
                <h2 class="text-base font-semibold text-gray-800">
                    @yield('title', 'Dashboard')
                </h2>
                <p class="text-xs text-gray-400">
                    {{ now()->locale('id')->translatedFormat('l, d F Y') }}
                </p>
            </div>
            <div class="flex items-center gap-4">
            {{-- Dark Mode Toggle --}}
            <button id="darkToggle" onclick="toggleDark()"
                class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 transition">
                <span id="darkIcon">🌙</span>
            </button>
            <div class="text-sm text-gray-500">
                Selamat datang, <span class="font-medium text-blue-600">{{ Auth::user()->name }}</span>
            </div>
        </div>
        </header>

        {{-- Halaman Content --}}
        <main class="flex-1 p-6">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="text-center text-xs text-gray-400 py-4 border-t border-gray-100">
            © {{ date('Y') }} Sistem Keuangan — Laravel 12
        </footer>

    </div>

    <script>
    // Cek preferensi dark mode
    if (localStorage.getItem('darkMode') === 'true') {
        document.documentElement.classList.add('dark');
        document.getElementById('darkIcon').textContent = '☀️';
    }

    function toggleDark() {
        const html = document.documentElement;
        const isDark = html.classList.toggle('dark');
        localStorage.setItem('darkMode', isDark);
        document.getElementById('darkIcon').textContent = isDark ? '☀️' : '🌙';
    }
</script>


</body>
</html>