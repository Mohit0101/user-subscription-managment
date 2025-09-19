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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('canceled_at')->nullable(); // for churn
            $table->string('status')->default('active'); // active|canceled|expired
            $table->string('promo_code')->nullable();    // v2 feature
            $table->unsignedInteger('promo_discount')->default(0); // in INR
            $table->timestamps();


            $table->index(['plan_id','status']);
            $table->index(['user_id','status']);
            $table->index(['created_at']);
            $table->index(['canceled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
