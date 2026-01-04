@extends('layouts.guest')
@section('title', 'Login - RMS')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center space-x-2 mb-4">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-amber-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
            </a>
            <h1 class="text-2xl font-bold text-white">Welcome Back</h1>
            <p class="text-slate-400">Sign in to your account</p>
        </div>

        <x-card>
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Email</label>
                    <x-input type="email" name="email" :value="old('email')" required autofocus />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Password</label>
                    <x-input type="password" name="password" required />
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded bg-slate-700 border-slate-600 text-amber-400 focus:ring-amber-400">
                        <span class="ml-2 text-sm text-slate-400">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-amber-400 hover:underline">Forgot password?</a>
                </div>
                <button type="submit" class="btn-primary w-full py-3">Sign In</button>
            </form>

            <p class="text-center text-slate-400 text-sm mt-6">
                Don't have an account? <a href="{{ route('register') }}" class="text-amber-400 hover:underline">Sign up</a>
            </p>
        </x-card>
    </div>
</div>
@endsection
