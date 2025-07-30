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
            $table->string('proof_of_delivery', 255)->nullable()->after('dp_receipt');
            $table->unsignedInteger('delivered_by_logistics')->nullable()->after('scheduled_at_logistics');
            $table->dateTime('delivered_at_logistics')->nullable()->after('delivered_by_logistics');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('proof_of_delivery');
            $table->dropColumn('delivered_by_logistics');
            $table->dropColumn('delivered_at_logistics');
        });
    }
};