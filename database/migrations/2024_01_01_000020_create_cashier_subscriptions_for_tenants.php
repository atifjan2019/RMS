<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashierSubscriptionsForTenants extends Migration
{
    public function up(): void
    {
        // Cashier expects a billable_id/billable_type pair for non-User billables.
        // This project uses Tenant as the billable model.
        if (!Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->string('type')->default('default');

                $table->morphs('billable'); // billable_id, billable_type

                $table->string('stripe_id')->unique();
                $table->string('stripe_status');
                $table->string('stripe_price')->nullable();
                $table->integer('quantity')->nullable();
                $table->timestamp('trial_ends_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->timestamps();

                $table->index(['billable_id', 'billable_type']);
            });
        }

        if (!Schema::hasTable('subscription_items')) {
            Schema::create('subscription_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
                $table->string('stripe_id')->unique();
                $table->string('stripe_product');
                $table->string('stripe_price');
                $table->integer('quantity')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->morphs('billable');
                $table->string('stripe_id')->nullable()->index();
                $table->string('pm_type')->nullable();
                $table->string('pm_last_four', 4)->nullable();
                $table->timestamp('trial_ends_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_items');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('customers');
    }
}
