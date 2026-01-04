@extends('layouts.guest')

@section('title', 'RMS - AI-Powered Review Management for Google Business Profile')
@section('description', 'Manage your Google Business Profile reviews with AI-powered reply suggestions. Respond faster, build trust, grow your business. Start for just $5/month.')

@section('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "name": "RMS - Reputation Management System",
    "applicationCategory": "BusinessApplication",
    "operatingSystem": "Web",
    "offers": {
        "@type": "Offer",
        "price": "5.00",
        "priceCurrency": "USD",
        "priceValidUntil": "2025-12-31"
    },
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.9",
        "ratingCount": "150"
    },
    "description": "AI-powered Google Business Profile review management. Respond to reviews faster with smart reply suggestions."
}
</script>
@endsection

@section('content')
<!-- Hero Section -->
<section class="relative min-h-[90vh] flex items-center overflow-hidden">
    <!-- Background gradient -->
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-900 to-amber-900/20"></div>

    <!-- Animated background elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-amber-400/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute top-1/2 -left-40 w-80 h-80 bg-amber-400/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-amber-400/10 border border-amber-400/20 text-amber-400 text-sm font-medium mb-6">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    Trusted by 500+ Businesses
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6">
                    Turn Google Reviews into
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-amber-200">Growth</span>
                </h1>

                <p class="text-lg sm:text-xl text-slate-300 mb-8 leading-relaxed">
                    AI-powered reply suggestions help you respond to every review professionally. Build trust, improve your reputation, and grow your business.
                </p>

                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('register') }}" class="btn-primary text-lg px-8 py-4">
                        Start Free Trial
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <a href="#features" class="btn-secondary text-lg px-8 py-4">
                        Learn More
                    </a>
                </div>

                <div class="mt-8 flex items-center gap-8 text-sm text-slate-400">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        No credit card required
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Cancel anytime
                    </div>
                </div>
            </div>

            <div class="relative">
                <!-- Dashboard mockup -->
                <div class="relative bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-slate-700 p-6 shadow-2xl">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    </div>

                    <!-- Sample review card -->
                    <div class="bg-slate-900/50 rounded-xl p-4 mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-slate-900 font-bold">J</div>
                                <div>
                                    <p class="font-medium text-white">John Smith</p>
                                    <div class="flex text-amber-400">
                                        @for($i = 0; $i < 5; $i++)
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <span class="badge-success">New</span>
                        </div>
                        <p class="text-slate-300 text-sm">"Excellent service! The team went above and beyond to help me. Highly recommended!"</p>
                    </div>

                    <!-- AI suggestion -->
                    <div class="bg-gradient-to-r from-amber-400/10 to-transparent rounded-xl p-4 border border-amber-400/20">
                        <div class="flex items-center gap-2 text-amber-400 text-sm font-medium mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            AI Draft Ready
                        </div>
                        <p class="text-slate-300 text-sm italic">"Thank you so much for your kind words, John! We're thrilled to hear about your positive experience..."</p>
                    </div>
                </div>

                <!-- Floating stats -->
                <div class="absolute -right-4 top-1/4 bg-slate-800 rounded-xl p-4 border border-slate-700 shadow-xl">
                    <p class="text-2xl font-bold text-amber-400">98%</p>
                    <p class="text-xs text-slate-400">Response Rate</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-20 bg-slate-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">
                Everything You Need to Manage Reviews
            </h2>
            <p class="text-lg text-slate-400 max-w-2xl mx-auto">
                Simple, powerful tools to help you respond to reviews faster and build a stellar reputation.
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">AI-Powered Replies</h3>
                <p class="text-slate-400">Generate professional reply drafts in seconds. Choose from friendly, professional, or recovery tones.</p>
            </div>

            <!-- Feature 2 -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">Real-Time Alerts</h3>
                <p class="text-slate-400">Get notified instantly when new reviews come in. Never miss a chance to engage with customers.</p>
            </div>

            <!-- Feature 3 -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">Analytics Dashboard</h3>
                <p class="text-slate-400">Track your reputation with detailed analytics. See trends, ratings, and response times at a glance.</p>
            </div>

            <!-- Feature 4 -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">Reply Templates</h3>
                <p class="text-slate-400">Save your best responses as templates. Respond consistently and efficiently.</p>
            </div>

            <!-- Feature 5 -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">Smart Filters</h3>
                <p class="text-slate-400">Filter by status, rating, or keywords. Prioritize negative reviews and respond quickly.</p>
            </div>

            <!-- Feature 6 -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">Secure & Private</h3>
                <p class="text-slate-400">Your data is encrypted at rest and in transit. We never share your information.</p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-20 bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">
                Get Started in 3 Simple Steps
            </h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-amber-400 to-amber-600 rounded-2xl flex items-center justify-center text-slate-900 text-2xl font-bold mx-auto mb-6">1</div>
                <h3 class="text-xl font-semibold text-white mb-2">Connect Google</h3>
                <p class="text-slate-400">Link your Google Business Profile with one click. Secure OAuth connection.</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-amber-400 to-amber-600 rounded-2xl flex items-center justify-center text-slate-900 text-2xl font-bold mx-auto mb-6">2</div>
                <h3 class="text-xl font-semibold text-white mb-2">Review Inbox</h3>
                <p class="text-slate-400">All your reviews in one place. Synced automatically, always up to date.</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-amber-400 to-amber-600 rounded-2xl flex items-center justify-center text-slate-900 text-2xl font-bold mx-auto mb-6">3</div>
                <h3 class="text-xl font-semibold text-white mb-2">Reply with AI</h3>
                <p class="text-slate-400">Generate smart replies instantly. Edit, approve, and post in seconds.</p>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-20 bg-slate-950" x-data="{ open: null }">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">
                Frequently Asked Questions
            </h2>
        </div>

        <div class="space-y-4">
            <!-- FAQ Item 1 -->
            <div class="bg-slate-900 rounded-xl border border-slate-800">
                <button @click="open = open === 1 ? null : 1" class="flex items-center justify-between w-full p-6 text-left">
                    <span class="font-medium text-white">How does the AI reply generation work?</span>
                    <svg class="w-5 h-5 text-amber-400 transition-transform" :class="open === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 1" x-collapse class="px-6 pb-6 text-slate-400">
                    Our AI analyzes the review content, rating, and context to generate appropriate reply suggestions. You can choose from friendly, professional, or recovery tones. All drafts require your approval before posting – we never auto-reply.
                </div>
            </div>

            <!-- FAQ Item 2 -->
            <div class="bg-slate-900 rounded-xl border border-slate-800">
                <button @click="open = open === 2 ? null : 2" class="flex items-center justify-between w-full p-6 text-left">
                    <span class="font-medium text-white">Is my Google Business data secure?</span>
                    <svg class="w-5 h-5 text-amber-400 transition-transform" :class="open === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 2" x-collapse class="px-6 pb-6 text-slate-400">
                    Absolutely. We use industry-standard encryption for all data at rest and in transit. Your Google tokens are encrypted and we only request the minimum permissions needed. You can disconnect your account at any time.
                </div>
            </div>

            <!-- FAQ Item 3 -->
            <div class="bg-slate-900 rounded-xl border border-slate-800">
                <button @click="open = open === 3 ? null : 3" class="flex items-center justify-between w-full p-6 text-left">
                    <span class="font-medium text-white">Can I manage multiple locations?</span>
                    <svg class="w-5 h-5 text-amber-400 transition-transform" :class="open === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 3" x-collapse class="px-6 pb-6 text-slate-400">
                    Currently, each subscription includes one active location. You can switch between locations or contact us for multi-location plans for businesses with multiple outlets.
                </div>
            </div>

            <!-- FAQ Item 4 -->
            <div class="bg-slate-900 rounded-xl border border-slate-800">
                <button @click="open = open === 4 ? null : 4" class="flex items-center justify-between w-full p-6 text-left">
                    <span class="font-medium text-white">What happens if I cancel?</span>
                    <svg class="w-5 h-5 text-amber-400 transition-transform" :class="open === 4 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 4" x-collapse class="px-6 pb-6 text-slate-400">
                    You can cancel anytime. You'll retain access until the end of your billing period. We don't do refunds for partial months, but we also don't hold you hostage with contracts.
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-br from-amber-400 to-amber-600">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 mb-4">
            Ready to Transform Your Reviews?
        </h2>
        <p class="text-lg text-slate-800 mb-8">
            Join hundreds of businesses already using RMS to manage their reputation.
        </p>
        <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 bg-slate-900 text-white font-semibold rounded-xl hover:bg-slate-800 transition-all duration-300 shadow-lg hover:shadow-xl">
            Start Your Free Trial
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </a>
    </div>
</section>
@endsection
