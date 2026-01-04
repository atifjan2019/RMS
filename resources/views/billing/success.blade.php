@extends('layouts.app')
@section('title', 'Subscription Success')
@section('page_title', 'Welcome!')

@section('content')
<div class="max-w-lg mx-auto text-center py-12">
    <div class="w-20 h-20 bg-green-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>
    <h1 class="text-3xl font-bold text-white mb-4">You're All Set!</h1>
    <p class="text-slate-400 mb-8">Your subscription is now active. Let's connect your Google Business Profile.</p>
    <a href="{{ route('google.index') }}" class="btn-primary text-lg px-8 py-4">Connect Google Now</a>
</div>
@endsection
