<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('action', 80)->default('system.legacy')->after('user_id');
            $table->text('description')->nullable()->after('action');
            $table->nullableMorphs('subject');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('properties')->nullable();
            $table->index(['action', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropIndex(['action', 'created_at']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropMorphs('subject');
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['action', 'description', 'ip_address', 'user_agent', 'properties']);
        });
    }
};
