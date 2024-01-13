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
        Schema::create('document_memo', function (Blueprint $table) {
            $table->foreignId('document_id')->constrained()->cascadeOnDelete()->comment('ドキュメントID');
            $table->foreignId('memo_id')->constrained()->cascadeOnDelete()->comment('メモID');
            $table->unique(['document_id', 'memo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_memo');
    }
};
