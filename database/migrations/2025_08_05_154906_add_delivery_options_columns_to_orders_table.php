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
        Schema::table('orders', function (Blueprint $table) {
          $table->text('incomplete_reason')->nullable()->after('payment_proof');
          $table->string('delivery_reference')->nullable()->after('carrier_name');
          $table->text('logistics_remarks')->nullable()->after('delivery_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('incomplete_reason');
            $table->dropColumn('delivery_reference');
            $table->dropColumn('logistics_remarks');
        });
    }
};