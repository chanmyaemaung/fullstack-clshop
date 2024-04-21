<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Modify the existing 'status' column to a string column
            $table->string('status')->default('pending')->change();
        });

        // Update 'declined' values to 'delivered'
        DB::statement("UPDATE orders SET status = 'delivered' WHERE status = 'declined'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the 'status' column back to its original definition
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'processing', 'shipped', 'declined', 'delivered', 'canceled'])
                ->default('pending')
                ->change();
        });

        // Update 'delivered' values back to 'declined'
        DB::statement("UPDATE orders SET status = 'declined' WHERE status = 'delivered'");
    }
};
