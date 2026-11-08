<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('records', function (Blueprint $table) {
            $table->date('due_at')->nullable()->after('document_date')->index();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable()->index();
            $table->timestamps();
            $table->index(['notifiable_type', 'notifiable_id', 'created_at'], 'notifications_owner_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::table('records', function (Blueprint $table) {
            $table->dropIndex(['due_at']);
            $table->dropColumn('due_at');
        });
    }
};
