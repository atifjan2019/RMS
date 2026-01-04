@extends('layouts.app')
@section('title', 'Checkout Cancelled')
@section('page_title', 'Checkout Cancelled')

@section('content')
<div class="max-w-lg mx-auto text-center py-12">
    <div class="w-20 h-20 bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </div>
    <h1 class="text-3xl font-bold text-white mb-4">Checkout Cancelled</h1>
    <p class="text-slate-400 mb-8">No worries! You can subscribe whenever you're ready.</p>
    <a href="{{ route('billing.plan') }}" class="btn-primary">Try Again</a>
</div>
@endsection
