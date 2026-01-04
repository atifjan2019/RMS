@extends('layouts.guest')
@section('title', 'Forgot Password - RMS')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-white">Reset Password</h1>
            <p class="text-slate-400">Enter your email to receive a reset link</p>
        </div>

        <x-card>
            @if(session('status'))
                <div class="mb-4 p-4 bg-green-500/10 border border-green-500/20 rounded-lg text-green-400 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Email</label>
                    <x-input type="email" name="email" :value="old('email')" required autofocus />
                    @error('email')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn-primary w-full py-3">Send Reset Link</button>
            </form>

            <p class="text-center text-slate-400 text-sm mt-6">
                <a href="{{ route('login') }}" class="text-amber-400 hover:underline">Back to login</a>
            </p>
        </x-card>
    </div>
</div>
@endsection
