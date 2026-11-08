<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Record;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_view_filtered_reports(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $category = Category::where('slug', 'letters')->firstOrFail();
        Record::create([
            'reference_number' => 'RR-REPORT-0001', 'title' => 'Incoming letter',
            'document_type' => 'incoming', 'category_id' => $category->id,
            'document_date' => '2026-11-01', 'status' => 'active', 'created_by' => $manager->id,
        ]);

        $this->actingAs($manager)->get(route('reports.index', ['type' => 'incoming']))
            ->assertOk()->assertSee('Reports & Analytics')->assertSee('Incoming letter')
            ->assertSee('RR-REPORT-0001');
    }

    public function test_viewer_cannot_access_reports(): void
    {
        $viewer = User::factory()->create(['role' => 'viewer', 'is_active' => true]);
        $this->actingAs($viewer)->get(route('reports.index'))->assertForbidden();
    }

    public function test_manager_can_export_records_as_csv(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        Record::create([
            'reference_number' => 'RR-CSV-0001', 'title' => 'Quarterly report',
            'document_type' => 'internal', 'document_date' => '2026-11-01',
            'status' => 'active', 'created_by' => $manager->id,
        ]);

        $response = $this->actingAs($manager)->get(route('reports.export'));
        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }
}
