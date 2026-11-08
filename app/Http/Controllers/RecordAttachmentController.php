<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\RecordAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RecordAttachmentController extends Controller
{
    private const ALLOWED_MIMES = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png',
    ];

    public function store(Request $request, Record $record): RedirectResponse
    {
        abort_if($record->status === 'archived', 404);

        $validated = $request->validate([
            'files' => ['required', 'array', 'min:1', 'max:10'],
            'files.*' => ['required', 'file', 'max:10240', 'mimes:'.implode(',', self::ALLOWED_MIMES)],
        ]);

        foreach ($validated['files'] as $file) {
            $path = $file->store('record-attachments/'.$record->id, 'local');

            $record->attachments()->create([
                'uploaded_by' => $request->user()->id,
                'original_name' => basename($file->getClientOriginalName()),
                'stored_path' => $path,
                'mime_type' => $file->getMimeType(),
                'size_bytes' => $file->getSize(),
            ]);
        }

        return redirect()->route('records.show', $record)->with('success', 'File(s) uploaded securely.');
    }

    public function download(Record $record, RecordAttachment $attachment): StreamedResponse
    {
        abort_unless($attachment->record_id === $record->id, 404);
        abort_unless(Storage::disk('local')->exists($attachment->stored_path), 404);

        return Storage::disk('local')->download(
            $attachment->stored_path,
            $attachment->original_name,
            ['Content-Type' => $attachment->mime_type ?: 'application/octet-stream']
        );
    }

    public function destroy(Request $request, Record $record, RecordAttachment $attachment): RedirectResponse
    {
        abort_unless($attachment->record_id === $record->id, 404);
        abort_if($record->status === 'archived', 404);

        Storage::disk('local')->delete($attachment->stored_path);
        $attachment->delete();

        return redirect()->route('records.show', $record)->with('success', 'Attachment deleted.');
    }
}
