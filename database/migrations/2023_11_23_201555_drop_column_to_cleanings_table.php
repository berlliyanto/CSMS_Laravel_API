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
            // $table->dropForeign(['cleaner_id']);
            // $table->dropColumn('cleaner_id');
            // $table->dropColumn('image_before');
            $table->dropColumn('image_progress');
            $table->dropColumn('image_finish');
            $table->dropColumn('status');
            $table->dropColumn('alasan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cleanings', function (Blueprint $table) {
            $table->unsignedBigInteger('cleaner_id');
            $table->string('image_before', 255)->nullable();
            $table->string('image_progress', 255)->nullable();
            $table->string('image_finish', 255)->nullable();
            $table->enum('status', ['Pending', 'On Progress', 'Finish', 'Not Finish']);
            $table->text('alasan')->nullable();
        });
    }
};
