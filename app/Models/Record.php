<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Record extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number', 'title', 'description', 'document_type',
        'category_id', 'department', 'document_date', 'status',
        'created_by', 'archived_at',
    ];

    protected function casts(): array
    {
        return ['document_date' => 'date', 'archived_at' => 'datetime'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
