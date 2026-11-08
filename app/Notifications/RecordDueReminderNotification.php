<?php

namespace App\Notifications;

use App\Models\Record;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RecordDueReminderNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Record $record) {}

    public function via(object $notifiable): array { return ['database']; }

    public function toArray(object $notifiable): array
    {
        return [
            'kind' => 'record_due',
            'title' => 'Document due date approaching',
            'message' => $this->record->reference_number.' — '.$this->record->title.' is due on '.$this->record->due_at->format('d M Y').'.',
            'record_id' => $this->record->id,
            'due_at' => $this->record->due_at->toDateString(),
            'url' => route('records.show', $this->record),
        ];
    }
}
