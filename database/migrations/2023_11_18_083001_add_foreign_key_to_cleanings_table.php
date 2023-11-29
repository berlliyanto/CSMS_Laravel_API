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
        Schema::table('cleanings', function (Blueprint $table) {
            $table->foreign('assign_by')->references('id')->on('users');
            $table->foreign('checked_by')->references('id')->on('users');
            $table->foreign('verified_by')->references('id')->on('users');
            $table->foreign('cleaner_id')->references('id')->on('users');
            $table->foreign('location_id')->references('id')->on('locations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cleanings', function (Blueprint $table) {
            $table->dropForeign(['assign_by']);
            $table->dropForeign(['checked_by']);
            $table->dropForeign(['verified_by']);
            $table->dropForeign(['cleaner_id']);
            $table->dropForeign(['location_id']);
        });
    }
};
