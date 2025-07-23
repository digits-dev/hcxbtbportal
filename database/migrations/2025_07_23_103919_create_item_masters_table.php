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
        Schema::create('item_masters', function (Blueprint $table) {
            $table->id();
            $table->string('digits_code', 8)->nullable();
            $table->string('item_description', 100)->nullable();
            $table->string('model', 50)->nullable();
            $table->string('actual_color', 50)->nullable();
            $table->string('size', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_masters');
    }
};
