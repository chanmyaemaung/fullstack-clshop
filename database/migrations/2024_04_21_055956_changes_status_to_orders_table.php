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
        // Increase the length of the 'status' column to accommodate the new value
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status', 20)->change(); // Change 20 to an appropriate length
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the changes made in the 'up' method
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->change(); // Revert to the original length
        });
    }
};
