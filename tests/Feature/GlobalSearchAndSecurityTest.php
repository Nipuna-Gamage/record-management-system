<?php

namespace Tests\Feature;

use App\Models\Record;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalSearchAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_search_records_by_reference_title_and_department(): void
    {
        $user = User::factory()->create();
        $record = Record::create([
            'reference_number' => 'RR-SEARCH-001',
            'title' => 'Annual Procurement Plan',
            'document_type' => 'internal',
            'department' => 'Finance Division',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)->get(route('search', ['q' => 'Procurement']))
            ->assertOk()->assertSee($record->reference_number)->assertSee($record->title);

        $this->get(route('search', ['q' => 'Finance']))
            ->assertOk()->assertSee($record->reference_number);
    }

    public function test_global_search_requires_at_least_two_characters(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('search', ['q' => 'x']))
            ->assertSessionHasErrors('q');
    }

    public function test_security_headers_are_added_to_application_responses(): void
    {
        $this->get('/login')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }
}
