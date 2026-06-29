@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">

    {{-- Alert sukses profil --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 border border-green-300 rounded-lg px-4 py-3 mb-4">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- Alert sukses password --}}
    @if(session('success_password'))
        <div class="bg-blue-100 text-blue-700 border border-blue-300 rounded-lg px-4 py-3 mb-4">
            🔒 {{ session('success_password') }}
        </div>
    @endif

    {{-- Form Update Profil --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">

        <div class="flex items-center gap-4 mb-6">
            <div class="w-14 h-14 rounded-full bg-blue-600 flex items-center justify-center text-white text-2xl font-bold">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-800">{{ $user->name }}</h2>
                <p class="text-sm text-gray-400">{{ $user->email }}</p>
            </div>
        </div>

        <h3 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
            Update Informasi Profil
        </h3>

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4 p-3 bg-gray-50 rounded-lg text-sm text-gray-500">
                <p>Akun dibuat: {{ $user->created_at->locale('id')->translatedFormat('d F Y') }}</p>
            </div>

            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium transition">
                Simpan Perubahan
            </button>
        </form>
    </div>

    {{-- Form Ganti Password --}}
    <div class="bg-white rounded-xl shadow p-6">

        <h3 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">
            🔒 Ganti Password
        </h3>

        <form action="{{ route('profile.password') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
                <input type="password" name="current_password"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('current_password') border-red-500 @enderror"
                    placeholder="Masukkan password lama">
                @error('current_password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" name="password"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                    placeholder="Minimal 8 karakter">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Ulangi password baru">
            </div>

            <button type="submit"
                class="bg-gray-700 hover:bg-gray-800 text-white px-5 py-2 rounded-lg font-medium transition">
                Ganti Password
            </button>
        </form>
    </div>

</div>
@endsection