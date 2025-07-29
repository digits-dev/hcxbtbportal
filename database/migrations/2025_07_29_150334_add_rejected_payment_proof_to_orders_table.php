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
            $table->string('rejected_payment_proof', 255)->nullable()->after('payment_proof');
            $table->unsignedInteger('rejected_by')->nullable()->after('verified_at_acctg');
            $table->dateTime('rejected_at')->nullable()->after('rejected_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('rejected_payment_proof');
            $table->dropColumn('rejected_by');
            $table->dropColumn('rejected_at');
        });
    }
};