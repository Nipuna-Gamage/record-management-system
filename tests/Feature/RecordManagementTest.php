<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Record;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_and_view_a_record(): void
    {
        $user = User::factory()->create(['role' => 'staff', 'is_active' => true]);
        $category = Category::where('slug', 'letters')->firstOrFail();

        $response = $this->actingAs($user)->post(route('records.store'), [
            'title' => 'Request for information',
            'document_type' => 'incoming',
            'category_id' => $category->id,
            'department' => 'Administration',
            'document_date' => '2026-11-01',
            'description' => 'A sample incoming document.',
        ]);

        $response->assertRedirect(route('records.index'));
        $this->assertDatabaseHas('records', [
            'title' => 'Request for information',
            'document_type' => 'incoming',
            'created_by' => $user->id,
            'status' => 'active',
        ]);

        $record = Record::firstOrFail();
        $this->actingAs($user)->get(route('records.show', $record))->assertOk()
            ->assertSee('Request for information');
    }

    public function test_guest_cannot_access_record_register(): void
    {
        $this->get(route('records.index'))->assertRedirect(route('login'));
    }

    public function test_record_can_be_archived_and_restored(): void
    {
        $user = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $record = Record::create([
            'reference_number' => 'RR-TEST-00001',
            'title' => 'Archive test',
            'document_type' => 'internal',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)->patch(route('records.archive', $record))
            ->assertRedirect(route('records.index', ['status' => 'archived']));
        $this->assertDatabaseHas('records', ['id' => $record->id, 'status' => 'archived']);

        $this->patch(route('records.restore', $record))
            ->assertRedirect(route('records.show', $record));
        $this->assertDatabaseHas('records', ['id' => $record->id, 'status' => 'active']);
    }
}
