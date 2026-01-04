# Reputation Management SaaS (RMS)

A lightweight Google Business Profile review management system built with Laravel 10.

## Stack

- **Backend**: Laravel 10
- **Auth**: Laravel Breeze (email/password)
- **Billing**: Stripe subscriptions ($5/month) via Laravel Cashier
- **Frontend**: Blade + Tailwind CSS + Alpine.js
- **Database**: MySQL
- **Cache + Queue**: Redis + Horizon

## Features

- 💳 Stripe billing with $5/month subscription
- 🔗 Google OAuth connection for Business Profile
- 📥 Reviews inbox with local storage for fast UI
- 🤖 AI-powered reply drafts (3 tones: friendly, professional, recovery)
- 🔔 Google Pub/Sub webhooks for real-time notifications
- ⏰ Hourly polling fallback for missed webhooks

## Installation

### 1. Prerequisites

- PHP 8.1+
- Composer
- Node.js 18+
- MySQL 8.0+
- Redis

### 2. Install Dependencies

```bash
composer install
npm install && npm run build
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure .env

```env
# App
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rms
DB_USERNAME=root
DB_PASSWORD=

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Queue
QUEUE_CONNECTION=redis

# Stripe
STRIPE_KEY=pk_test_xxxxx
STRIPE_SECRET=sk_test_xxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxx
CASHIER_CURRENCY=usd

# Google OAuth
GOOGLE_CLIENT_ID=xxxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=xxxxx
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# OpenAI (for AI drafts)
OPENAI_API_KEY=sk-xxxxx

# Webhook Secret (for Pub/Sub)
WEBHOOK_SECRET=your-secret-token
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Start Development Servers

```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Horizon (queue worker)
php artisan horizon

# Terminal 3: Vite (for dev only)
npm run dev
```

### 7. Stripe Webhook (Local Development)

```bash
# Install Stripe CLI and run:
stripe listen --forward-to localhost:8000/stripe/webhook
```

Use the webhook signing secret from the CLI output in your `.env`.

### 8. Google OAuth Setup

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing
3. Enable these APIs:
   - Google My Business API
   - Google My Business Accounts Management API
4. Create OAuth 2.0 credentials
5. Add authorized redirect URI: `http://localhost:8000/auth/google/callback`
6. Copy Client ID and Secret to `.env`

## Production Deployment

```bash
# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Start Horizon
php artisan horizon
```

## Architecture

- All Google API calls run via queued jobs (never during page render)
- Multi-tenant: 1 business per paying account
- All records are tenant-scoped for data isolation
- Reviews are stored locally for fast UI rendering

## License

Proprietary - All Rights Reserved
