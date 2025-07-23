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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 20)->nullable();
            $table->integer('status')->nullable();
            $table->string('customer_name', 50)->nullable();
            $table->string('delivery_address', 255)->nullable();
            $table->string('email_address', 255)->nullable();
            $table->string('contact_details', 255)->nullable();
            $table->string('has_downpayment', 10)->nullable();
            $table->decimal('downpayment_value', 18, 2)->nullable();
            $table->decimal('financed_amount', 18, 2)->nullable();
            $table->integer('item_id')->nullable();
            $table->string('approved_contract', 255)->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};