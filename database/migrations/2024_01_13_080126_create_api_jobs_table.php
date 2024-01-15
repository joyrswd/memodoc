<?php

use App\Repositories\ApiJobRepository;
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
        Schema::create('api_jobs', function (Blueprint $table) {
            $table->id()->comment('APIジョブID');
            $table->string('api_name', 20)->nullable()->index()->comment('API名');
            $table->string('status', 10)->index()->comment('API実行状況');
            $table->text('response')->nullable()->comment('処理のレスポンス');
            $table->string('error_message')->nullable()->comment('エラーメッセージ');
            $table->foreignId('user_id')->constrained()->comment('ユーザーID');
            $table->timestamp('started_at')->nullable()->comment('API実行開始日時');
            $table->timestamp('finished_at')->nullable()->comment('API実行開始日時');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_jobs');
    }
};
