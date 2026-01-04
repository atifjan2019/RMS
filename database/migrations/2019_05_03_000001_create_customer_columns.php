<?php

// Cashier default migration (adds Stripe columns to `users`).
// This project bills Tenants instead, so we intentionally skip this migration.

use Illuminate\Database\Migrations\Migration;

class CreateCustomerColumns extends Migration
{
    public function up(): void
    {
        // no-op
    }

    public function down(): void
    {
        // no-op
    }
}
