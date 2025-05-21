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
        Schema::create('permission_role', function (Blueprint $table) {
            $table->uuid('permission_uuid');
            $table->uuid('role_uuid');
            $table->timestamps();

            $table->foreign('permission_uuid')
                ->references('uuid')
                ->on('permissions')
                ->onDelete('cascade');

            $table->foreign('role_uuid')
                ->references('uuid')
                ->on('roles')
                ->onDelete('cascade');

            $table->primary(['permission_uuid', 'role_uuid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_role');
    }
}; 