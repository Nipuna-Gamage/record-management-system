<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Record;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RecordAttachmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_upload_and_download_a_record_attachment(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['role' => 'staff', 'is_active' => true]);
        $record = Record::create([
            'reference_number' => 'RR-TEST-001',
            'title' => 'Test document',
            'document_type' => 'internal',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $file = UploadedFile::fake()->create('minutes.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->post(route('records.attachments.store', $record), [
            'files' => [$file],
        ]);

        $response->assertRedirect(route('records.show', $record));
        $this->assertDatabaseHas('record_attachments', [
            'record_id' => $record->id,
            'original_name' => 'minutes.pdf',
        ]);

        $attachment = $record->attachments()->firstOrFail();
        Storage::disk('local')->assertExists($attachment->stored_path);

        $this->actingAs($user)
            ->get(route('records.attachments.download', [$record, $attachment]))
            ->assertOk();
    }

    public function test_viewer_cannot_upload_an_attachment(): void
    {
        Storage::fake('local');
        $viewer = User::factory()->create(['role' => 'viewer', 'is_active' => true]);
        $record = Record::create([
            'reference_number' => 'RR-TEST-002',
            'title' => 'Viewer document',
            'document_type' => 'internal',
            'status' => 'active',
            'created_by' => $viewer->id,
        ]);

        $this->actingAs($viewer)
            ->post(route('records.attachments.store', $record), [
                'files' => [UploadedFile::fake()->create('minutes.pdf', 100, 'application/pdf')],
            ])
            ->assertForbidden();
    }

    public function test_attachment_must_belong_to_the_record_in_the_url(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['role' => 'viewer', 'is_active' => true]);
        $one = Record::create([
            'reference_number' => 'RR-TEST-003', 'title' => 'One',
            'document_type' => 'internal', 'status' => 'active', 'created_by' => $user->id,
        ]);
        $two = Record::create([
            'reference_number' => 'RR-TEST-004', 'title' => 'Two',
            'document_type' => 'internal', 'status' => 'active', 'created_by' => $user->id,
        ]);
        $attachment = $one->attachments()->create([
            'uploaded_by' => $user->id, 'original_name' => 'private.pdf',
            'stored_path' => 'record-attachments/1/private.pdf',
            'mime_type' => 'application/pdf', 'size_bytes' => 100,
        ]);

        $this->actingAs($user)
            ->get(route('records.attachments.download', [$two, $attachment]))
            ->assertNotFound();
    }
}
