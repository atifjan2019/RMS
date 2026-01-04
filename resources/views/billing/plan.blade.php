@extends('layouts.app')

@section('title', 'Subscription')
@section('page_title', 'Subscription')

@section('content')
<div class="max-w-2xl mx-auto">
    <x-card>
        @if($isSubscribed)
            <!-- Subscribed State -->
            <div class="text-center py-4">
                <div class="w-16 h-16 bg-green-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-white mb-2">You're Subscribed!</h2>
                <p class="text-slate-400 mb-4">Your Pro plan is active.</p>
                <a href="{{ route('billing.manage') }}" class="btn-secondary">Manage Subscription</a>
            </div>
        @else
            <!-- Not Subscribed State -->
            <div class="text-center py-4">
                <h2 class="text-2xl font-bold text-white mb-2">Subscribe to RMS Pro</h2>
                <p class="text-slate-400 mb-6">
                    Get access to all features including AI-powered reply drafts.
                </p>

                <!-- Pricing Card -->
                <div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-2xl p-6 mb-6 text-left">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-white">Pro Plan</h3>
                            <p class="text-slate-400">Everything you need</p>
                        </div>
                        <div class="text-right">
                            <span class="text-3xl font-bold text-amber-400">$5</span>
                            <span class="text-slate-400">/month</span>
                        </div>
                    </div>

                    <ul class="space-y-2 mb-6">
                        <li class="flex items-center text-slate-300 text-sm">
                            <svg class="w-4 h-4 text-amber-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            1 Google Business Profile
                        </li>
                        <li class="flex items-center text-slate-300 text-sm">
                            <svg class="w-4 h-4 text-amber-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Unlimited AI Reply Drafts
                        </li>
                        <li class="flex items-center text-slate-300 text-sm">
                            <svg class="w-4 h-4 text-amber-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Real-time Notifications
                        </li>
                        <li class="flex items-center text-slate-300 text-sm">
                            <svg class="w-4 h-4 text-amber-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Reply Templates
                        </li>
                    </ul>

                    <form action="{{ route('billing.checkout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-primary w-full text-lg py-4">
                            Subscribe Now
                        </button>
                    </form>
                </div>

                <p class="text-slate-500 text-sm">
                    Secure payment via Stripe. Cancel anytime.
                </p>
            </div>
        @endif
    </x-card>
</div>
@endsection
