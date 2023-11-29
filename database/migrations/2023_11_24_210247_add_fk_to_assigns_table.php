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
        Schema::table('assigns', function (Blueprint $table) {
            $table->foreign('assign_by')->references('id')->on('users');
            $table->foreign('area_id')->references('id')->on('areas');
            $table->foreign('supervisor_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assigns', function (Blueprint $table) {
            $table->dropForeign(['assign_by']);
            $table->dropForeign(['area_id']);
            $table->dropForeign(['supervisor_id']);
        });
    }
};
