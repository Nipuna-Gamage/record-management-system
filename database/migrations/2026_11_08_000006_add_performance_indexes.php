<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('records', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'records_status_created_idx');
            $table->index(['category_id', 'status'], 'records_category_status_idx');
            $table->index(['created_by', 'created_at'], 'records_creator_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('records', function (Blueprint $table) {
            $table->dropIndex('records_status_created_idx');
            $table->dropIndex('records_category_status_idx');
            $table->dropIndex('records_creator_created_idx');
        });
    }
};
