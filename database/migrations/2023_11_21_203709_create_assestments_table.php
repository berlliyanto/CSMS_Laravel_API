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
        Schema::create('assestments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('leader')->required();
            $table->unsignedBigInteger('cleaner')->required();
            $table->unsignedBigInteger('location')->required();
            $table->integer('plk_s')->required();
            $table->integer('plk_ddb')->required();
            $table->integer('sik_mptu')->required();
            $table->integer('sik_ktp')->required();
            $table->integer('sik_kdtma')->required();
            $table->integer('sik_mw')->required();
            $table->integer('sik_rmtp')->required();
            $table->integer('pnm_r')->required();
            $table->integer('pnm_mslc')->required();
            $table->integer('pnm_q')->required();
            $table->integer('tj_ktw')->required();
            $table->integer('tj_kwdmp')->required();
            $table->integer('tj_kd')->required();
            $table->integer('tj_mpsj')->required();
            $table->integer('tj_mpmp')->required();
            $table->integer('kom_k')->required();
            $table->integer('kom_p')->required();
            $table->integer('kom_kdb')->required();
            $table->integer('kom_ptp')->required();
            $table->integer('kom_kmk')->required();
            $table->integer('kom_s')->required();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assestments');
    }
};
