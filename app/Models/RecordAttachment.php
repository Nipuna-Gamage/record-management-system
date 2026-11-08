<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecordAttachment extends Model
{
    protected $fillable = [
        'record_id', 'uploaded_by', 'original_name', 'stored_path', 'mime_type', 'size_bytes',
    ];

    public function record(): BelongsTo
    {
        return $this->belongsTo(Record::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
