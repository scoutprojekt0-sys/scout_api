<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Subscription Plans
        if (!Schema::hasTable('subscription_plans')) {
            Schema::create('subscription_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Free, Scout Pro, Manager Pro, Club Premium
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->enum('billing_cycle', ['monthly', 'yearly', 'lifetime'])->default('monthly');
                $table->json('features'); // Limit details
                $table->integer('profile_views_limit')->default(10);
                $table->integer('messages_limit')->default(5);
                $table->integer('video_views_limit')->default(20);
                $table->boolean('anonymous_messaging')->default(false);
                $table->boolean('advanced_filters')->default(false);
                $table->boolean('ai_recommendations')->default(false);
                $table->boolean('api_access')->default(false);
                $table->boolean('priority_support')->default(false);
                $table->boolean('no_ads')->default(false);
                $table->integer('team_members')->default(1);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // User Subscriptions
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained();
            $table->string('stripe_subscription_id')->nullable();
            $table->enum('status', ['active', 'cancelled', 'expired', 'suspended'])->default('active');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained();
            $table->string('payment_gateway'); // stripe, paypal, iyzico
            $table->string('transaction_id')->unique();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->enum('payment_method', ['card', 'bank_transfer', 'wallet'])->default('card');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('transaction_id');
        });

        // Invoices
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained();
            $table->string('invoice_number')->unique();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->json('billing_details'); // name, address, tax_id
            $table->json('line_items'); // itemized list
            $table->text('notes')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('invoice_number');
        });

        // Payment Methods (Saved Cards)
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('stripe_payment_method_id')->nullable();
            $table->enum('type', ['card', 'bank_account'])->default('card');
            $table->string('card_brand')->nullable(); // visa, mastercard
            $table->string('card_last_four', 4)->nullable();
            $table->integer('card_exp_month')->nullable();
            $table->integer('card_exp_year')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index('user_id');
        });

        // Usage Tracking (Daily limits)
        Schema::create('subscription_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('usage_date');
            $table->integer('profile_views_count')->default(0);
            $table->integer('messages_sent_count')->default(0);
            $table->integer('video_views_count')->default(0);
            $table->integer('api_calls_count')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'usage_date']);
            $table->index('usage_date');
        });

        // Referral System
        if (!Schema::hasTable('referrals')) {
            Schema::create('referrals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('referred_id')->constrained('users')->onDelete('cascade');
                $table->string('referral_code', 20)->unique();
                $table->decimal('commission_amount', 10, 2)->default(0);
                $table->enum('status', ['pending', 'completed', 'paid'])->default('pending');
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->index('referral_code');
            });
        }

        // Commission Tracking
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->constrained();
            $table->decimal('amount', 10, 2);
            $table->decimal('percentage', 5, 2); // Platform commission %
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
        // Shared referrals table may be owned by another migration.
        Schema::dropIfExists('subscription_usage');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('subscriptions');
        // Shared subscription_plans table may be owned by another migration.
    }
};
