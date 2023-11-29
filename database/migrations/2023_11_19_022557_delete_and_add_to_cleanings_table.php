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
            $table->dropForeign(['checked_by']);
            $table->dropColumn('checked_by');
            $table->timestamp('verified_danone_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cleanings', function (Blueprint $table) {
            $table->unsignedBigInteger('checked_by');
            $table->foreign('checked_by')->references('id')->on('users');
            $table->dropColumn('verified_danone_at');
        });
    }
};
