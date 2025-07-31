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
        Schema::create('order_histories', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 20)->nullable();
            $table->integer('status')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('delivery_address')->nullable();
            $table->string('email_address')->nullable();
            $table->string('contact_details')->nullable();
            $table->string('has_downpayment', 10)->nullable();
            $table->decimal('downpayment_value', 18, 2)->nullable();
            $table->decimal('financed_amount', 18, 2)->nullable();
            $table->string('approved_contract')->nullable();
            $table->string('payment_proof')->nullable();
            $table->string('rejected_payment_proof')->nullable();
            $table->string('dp_receipt')->nullable();
            $table->string('proof_of_delivery')->nullable();
            $table->dateTime('schedule_date')->nullable();
            $table->string('transaction_type', 50)->nullable();
            $table->string('carrier_name', 100)->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('verified_by_acctg')->nullable();
            $table->dateTime('verified_at_acctg')->nullable();
            $table->integer('rejected_by')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->integer('scheduled_by_logistics')->nullable();
            $table->dateTime('scheduled_at_logistics')->nullable();
            $table->integer('delivered_by_logistics')->nullable();
            $table->dateTime('delivered_at_logistics')->nullable();
            $table->integer('closed_by_ecomm')->nullable();
            $table->dateTime('closed_at_ecomm')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_histories');
    }
};
