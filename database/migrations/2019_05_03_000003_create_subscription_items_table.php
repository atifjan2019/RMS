<?php

// Cashier default migration (creates `subscription_items` for user-based billing).
// This project bills Tenants instead, so we intentionally skip this migration.

use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionItemsTable extends Migration
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
