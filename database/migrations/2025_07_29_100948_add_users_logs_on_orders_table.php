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
             $table->unsignedInteger('verified_by_acctg')->nullable();
             $table->dateTime('verified_at_acctg')->nullable();
             $table->unsignedInteger('scheduled_by_logistics')->nullable();
             $table->dateTime('scheduled_at_logistics')->nullable();
             $table->unsignedInteger('closed_by_ecomm')->nullable();
             $table->dateTime('closed_at_ecomm')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
           $table->dropColumn('verified_by_acctg');
           $table->dropColumn('verified_at_acctg');
           $table->dropColumn('scheduled_by_logistics');
           $table->dropColumn('scheduled_at_logistics');
           $table->dropColumn('closed_by_ecomm');
           $table->dropColumn('closed_at_ecomm');
        });
    }
};