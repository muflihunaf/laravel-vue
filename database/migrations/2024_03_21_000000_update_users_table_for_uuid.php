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
        Schema::table('users', function (Blueprint $table) {
            // Drop the existing id column
            $table->dropColumn('id');
            
            // Add uuid column as primary key
            $table->uuid('uuid')->primary()->first();
            
            // Add soft deletes
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove soft deletes
            $table->dropSoftDeletes();
            
            // Remove uuid column
            $table->dropColumn('uuid');
            
            // Add back the id column
            $table->id()->first();
        });
    }
}; 