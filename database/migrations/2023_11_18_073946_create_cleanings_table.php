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
        Schema::create('cleanings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assign_by');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('cleaner_id');
            $table->string('image_before', 255)->nullable();
            $table->string('image_progress', 255)->nullable();
            $table->string('image_finish', 255)->nullable();
            $table->enum('status', ['Pending', 'On Progress', 'Finish', 'Not Finish']);
            $table->text('alasan')->nullable();
            $table->unsignedBigInteger('verified_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cleanings');
    }
};
