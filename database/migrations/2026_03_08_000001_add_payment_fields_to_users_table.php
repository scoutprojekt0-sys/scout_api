<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable()->after('phone');
            $table->enum('subscription_status', ['active', 'inactive', 'cancelled', 'expired'])->default('inactive')->after('stripe_customer_id');
            $table->timestamp('subscription_end_date')->nullable()->after('subscription_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['stripe_customer_id', 'subscription_status', 'subscription_end_date']);
        });
    }
};
