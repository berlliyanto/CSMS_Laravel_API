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
        Schema::create('assigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assign_by');
            $table->unsignedBigInteger('area_id');
            $table->longText('task')->nullable();
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->timestamp('checked_supervisor_at')->nullable();
            $table->timestamp('verified_danone_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assigns');
    }
};
