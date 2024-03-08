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
        Schema::create('reports', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('post_id', 36);
            $table->uuid('reported_by');
            $table->enum('reason', ['S', 'I', 'O', 'Oth'])->comment('S: Spam, I: Inappropriate, O: Offensive, Oth: Other');
            $table->string('other_reason', 512)->nullable();

            $table->foreign('post_id')->references('id')->on('posts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('reported_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();

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
        Schema::dropIfExists('reports');
    }
};
