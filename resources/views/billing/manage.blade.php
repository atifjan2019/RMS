@extends('layouts.app')

@section('title', 'Manage Subscription')
@section('page_title', 'Manage Subscription')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <x-card>
        <h2 class="text-lg font-semibold text-white mb-4">Subscription Status</h2>

        @if($subscription && $subscription->active())
            <div class="flex items-center justify-between p-4 bg-green-500/10 rounded-xl border border-green-500/20 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-500/20 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-white">Pro Plan - Active</p>
                    </div>
                </div>
                <p class="text-2xl font-bold text-white">$5<span class="text-sm text-slate-400">/mo</span></p>
            </div>

            <div class="flex gap-3">
                <form action="{{ route('billing.portal') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-secondary">Update Payment</button>
                </form>
                <form action="{{ route('billing.cancel-subscription') }}" method="POST" onsubmit="return confirm('Cancel subscription?');">
                    @csrf
                    <button type="submit" class="btn-secondary text-red-400">Cancel</button>
                </form>
            </div>
        @else
            <p class="text-slate-400 mb-4">No active subscription.</p>
            <a href="{{ route('billing.plan') }}" class="btn-primary">Subscribe Now</a>
        @endif
    </x-card>
</div>
@endsection
