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
           $table->dateTime('schedule_date')->nullable()->after('dp_receipt');
           $table->string('transaction_type', 50)->nullable()->after('schedule_date');
           $table->string('carrier_name', 100)->nullable()->after('transaction_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('schedule_date');
                $table->dropColumn('transaction_type');
        });
    }
};