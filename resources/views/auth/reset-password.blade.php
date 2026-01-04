@extends('layouts.guest')
@section('title', 'Reset Password - RMS')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-white">Set New Password</h1>
        </div>

        <x-card>
            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Email</label>
                    <x-input type="email" name="email" :value="old('email', $request->email)" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">New Password</label>
                    <x-input type="password" name="password" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Confirm Password</label>
                    <x-input type="password" name="password_confirmation" required />
                </div>
                <button type="submit" class="btn-primary w-full py-3">Reset Password</button>
            </form>
        </x-card>
    </div>
</div>
@endsection
