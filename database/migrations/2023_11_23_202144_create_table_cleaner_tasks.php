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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cleaner_id');
            $table->unsignedBigInteger('cleaning_id');
            $table->string('image_before', 255)->nullable();
            $table->string('image_progress', 255)->nullable();
            $table->string('image_finish', 255)->nullable();
            $table->enum('status', ['Pending', 'On Progress', 'Finish', 'Not Finish'])->nullable();
            $table->text('alasan')->nullable();
            $table->timestamps();

            $table->foreign('cleaner_id')->references('id')->on('users');
            $table->foreign('cleaning_id')->references('id')->on('cleanings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
