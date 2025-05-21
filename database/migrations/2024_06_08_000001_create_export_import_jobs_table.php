<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('export_import_jobs', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('user_uuid');
            $table->string('type'); // export or import
            $table->string('model'); // books, authors, categories
            $table->json('fields');
            $table->string('filename')->nullable();
            $table->string('filepath')->nullable();
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->text('message')->nullable(); // error or info message
            $table->timestamps();

            $table->foreign('user_uuid')->references('uuid')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('export_import_jobs');
    }
}; 