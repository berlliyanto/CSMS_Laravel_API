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
        Schema::table('assestments', function (Blueprint $table) {
            $table->foreign('leader')->references('id')->on('users');
            $table->foreign('cleaner')->references('id')->on('users');
            $table->foreign('location')->references('id')->on('locations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assestments', function (Blueprint $table) {
            $table->dropForeign(['leader']);
            $table->dropForeign(['cleaner']);
            $table->dropForeign(['location']);
        });
    }
};
