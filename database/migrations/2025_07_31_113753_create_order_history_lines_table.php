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
        Schema::create('order_history_lines', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->nullable();
            $table->integer('digits_code')->nullable();
            $table->string('serial_no', 50)->nullable();
            $table->string('imei', 50)->nullable();
            $table->integer('qty')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_history_lines');
    }
};
