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
        Schema::create('users', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('first_name', 64);
            $table->string('last_name', 64);
            $table->string('email', 128)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('role', ['A', 'U'])->comment('Admin, User');
            $table->string('password', 64);
            $table->string('phone', 15)->nullable();
            $table->enum('status', ['P', 'A', 'R'])->default('P');
            $table->string('token', 32)->nullable();
            $table->string('profile_image_url', 128)->nullable();
            $table->rememberToken();

            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->char('deleted_by', 36)->nullable();
            $table->timestamps(); // Adds created_at and updated_at columns
            $table->softDeletes(); // Adds deleted_at Datatype Timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};