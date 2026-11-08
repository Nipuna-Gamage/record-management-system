<?php

namespace App\Console\Commands;

use App\Models\Record;
use App\Models\User;
use App\Notifications\RecordDueReminderNotification;
use Illuminate\Console\Command;

class SendRecordDueReminders extends Command
{
    protected $signature = 'records:send-due-reminders';
    protected $description = 'Notify record owners and managers about documents due within the next three days';

    public function handle(): int
    {
        $records = Record::query()
            ->where('status', 'active')
            ->whereNotNull('due_at')
            ->whereDate('due_at', '>=', today())
            ->whereDate('due_at', '<=', today()->addDays(3))
            ->with('creator')
            ->get();

        $recipients = User::query()->where('is_active', true)->whereIn('role', ['admin', 'manager'])->get();
        $sent = 0;

        foreach ($records as $record) {
            $targets = $recipients->push($record->creator)->filter()->unique('id');
            foreach ($targets as $user) {
                $alreadySent = $user->notifications()
                    ->where('type', RecordDueReminderNotification::class)
                    ->whereDate('created_at', today())
                    ->where('data->record_id', $record->id)
                    ->exists();

                if (!$alreadySent) {
                    $user->notify(new RecordDueReminderNotification($record));
                    $sent++;
                }
            }
        }

        $this->info("Sent {$sent} due reminder(s).");
        return self::SUCCESS;
    }
}
