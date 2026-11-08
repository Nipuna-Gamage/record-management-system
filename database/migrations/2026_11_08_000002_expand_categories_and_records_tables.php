<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name', 120)->unique();
            $table->string('slug', 140)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
        });

        DB::table('categories')->insert([
            ['name' => 'General', 'slug' => 'general', 'description' => 'General documents', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Letters', 'slug' => 'letters', 'description' => 'Incoming and outgoing letters', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Reports', 'slug' => 'reports', 'description' => 'Reports and summaries', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Finance', 'slug' => 'finance', 'description' => 'Financial documents', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::table('records', function (Blueprint $table) {
            $table->string('reference_number', 40)->unique();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->string('document_type', 20)->default('internal');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('department', 120)->nullable();
            $table->date('document_date')->nullable();
            $table->string('status', 20)->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('archived_at')->nullable();
            $table->index(['status', 'document_type']);
            $table->index('document_date');
        });
    }

    public function down(): void
    {
        Schema::table('records', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['created_by']);
            $table->dropIndex(['status', 'document_type']);
            $table->dropIndex(['document_date']);
            $table->dropUnique(['reference_number']);
            $table->dropColumn(['reference_number', 'title', 'description', 'document_type', 'category_id', 'department', 'document_date', 'status', 'created_by', 'archived_at']);
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->dropUnique(['slug']);
            $table->dropColumn(['name', 'slug', 'description', 'is_active']);
        });
    }
};
