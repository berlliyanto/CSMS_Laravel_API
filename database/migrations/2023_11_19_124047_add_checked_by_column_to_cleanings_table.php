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
            $table->timestamp('checked_supervisor_at')->nullable()->after('verified_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cleanings', function (Blueprint $table) {
            $table->dropColumn('checked_supervisor_at');
        });
    }
};
