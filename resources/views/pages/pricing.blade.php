@extends('layouts.guest')

@section('title', 'Pricing - RMS Reputation Management')
@section('description', 'Simple, affordable pricing for Google Business Profile review management. Just $5/month for unlimited AI-powered replies.')

@section('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "RMS Pro Plan",
    "description": "Complete review management with AI-powered replies",
    "offers": {
        "@type": "Offer",
        "price": "5.00",
        "priceCurrency": "USD",
        "availability": "https://schema.org/InStock"
    }
}
</script>
@endsection

@section('content')
<section class="py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-4xl sm:text-5xl font-bold text-white mb-4">
                Simple, Transparent Pricing
            </h1>
            <p class="text-lg text-slate-400">
                Everything you need to manage your reputation. No hidden fees.
            </p>
        </div>

        <!-- Pricing Card -->
        <div class="max-w-md mx-auto">
            <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl border border-slate-700 p-8 relative overflow-hidden">
                <!-- Popular badge -->
                <div class="absolute top-0 right-0">
                    <div class="bg-gradient-to-r from-amber-400 to-amber-500 text-slate-900 text-xs font-bold px-4 py-1 rounded-bl-xl">
                        BEST VALUE
                    </div>
                </div>

                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-white mb-2">Pro Plan</h2>
                    <div class="flex items-baseline justify-center">
                        <span class="text-5xl font-bold text-white">$5</span>
                        <span class="text-slate-400 ml-2">/month</span>
                    </div>
                    <p class="text-slate-400 mt-2">Billed monthly. Cancel anytime.</p>
                </div>

                <ul class="space-y-4 mb-8">
                    <li class="flex items-center text-slate-300">
                        <svg class="w-5 h-5 text-amber-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Connect 1 Google Business Profile
                    </li>
                    <li class="flex items-center text-slate-300">
                        <svg class="w-5 h-5 text-amber-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Unlimited AI Reply Drafts
                    </li>
                    <li class="flex items-center text-slate-300">
                        <svg class="w-5 h-5 text-amber-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        3 Tone Options (Friendly, Professional, Recovery)
                    </li>
                    <li class="flex items-center text-slate-300">
                        <svg class="w-5 h-5 text-amber-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Real-time Review Notifications
                    </li>
                    <li class="flex items-center text-slate-300">
                        <svg class="w-5 h-5 text-amber-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Reply Templates
                    </li>
                    <li class="flex items-center text-slate-300">
                        <svg class="w-5 h-5 text-amber-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Analytics Dashboard
                    </li>
                    <li class="flex items-center text-slate-300">
                        <svg class="w-5 h-5 text-amber-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Email Support
                    </li>
                </ul>

                <a href="{{ route('register') }}" class="btn-primary w-full text-center text-lg py-4">
                    Get Started Now
                </a>

                <p class="text-center text-slate-500 text-sm mt-4">
                    7-day free trial included
                </p>
            </div>
        </div>

        <!-- Trust badges -->
        <div class="mt-12 text-center">
            <div class="flex flex-wrap justify-center gap-8 text-slate-500 text-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Secure Payment via Stripe
                </div>
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Cancel Anytime
                </div>
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Email Support
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
