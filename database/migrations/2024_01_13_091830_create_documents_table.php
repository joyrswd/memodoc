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
        Schema::create('documents', function (Blueprint $table) {
            $table->id()->comment('ドキュメントID');
            $table->string('title', 255)->comment('タイトル');
            $table->text('content')->nullable()->comment('内容');
            $table->foreignId('user_id')->constrained()->comment('ユーザーID');
            $table->foreignId('api_job_id')->constrained()->comment('APIジョブID');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
