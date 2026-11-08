<?php

namespace App\Notifications;

use App\Models\Record;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RecordCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Record $record) {}

    public function via(object $notifiable): array { return ['database']; }

    public function toArray(object $notifiable): array
    {
        return [
            'kind' => 'record_created',
            'title' => 'New record added',
            'message' => $this->record->reference_number.' — '.$this->record->title,
            'record_id' => $this->record->id,
            'url' => route('records.show', $this->record),
        ];
    }
}
