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
        Schema::create('api_job_memo', function (Blueprint $table) {
            $table->foreignId('api_job_id')->constrained()->cascadeOnDelete()->comment('APIジョブID');
            $table->foreignId('memo_id')->constrained()->cascadeOnDelete()->comment('メモID');
            $table->tinyInteger('order')->default(1)->comment('パーツ内の順番');
            $table->unique(['api_job_id', 'memo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_job_memo');
    }
};
