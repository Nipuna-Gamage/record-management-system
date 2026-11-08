<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('record_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('original_name', 255);
            $table->string('stored_path', 500);
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('size_bytes');
            $table->timestamps();
            $table->index(['record_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('record_attachments');
    }
};
