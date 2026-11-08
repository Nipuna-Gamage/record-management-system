<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->validatedFilters($request);
        $query = $this->filteredQuery($filters);

        $total = (clone $query)->count();
        $active = (clone $query)->where('status', 'active')->count();
        $archived = (clone $query)->where('status', 'archived')->count();
        $byType = (clone $query)->selectRaw('document_type, COUNT(*) as total')
            ->groupBy('document_type')->orderBy('document_type')->get();
        $byCategory = (clone $query)->leftJoin('categories', 'records.category_id', '=', 'categories.id')
            ->selectRaw("COALESCE(categories.name, 'Uncategorised') as category_name, COUNT(records.id) as total")
            ->groupBy('categories.name')->orderByDesc('total')->get();

        $monthly = collect(range(5, 0))->map(function (int $offset) use ($filters) {
            $month = Carbon::now()->startOfMonth()->subMonths($offset);
            $query = Record::query()->whereBetween('document_date', [$month->copy()->startOfMonth()->toDateString(), $month->copy()->endOfMonth()->toDateString()]);
            $this->applyFilters($query, $filters, false);
            return (object) ['label' => $month->format('M Y'), 'total' => $query->count()];
        });

        $records = (clone $query)->with(['category', 'creator'])->latest()->paginate(15)->withQueryString();

        return view('reports.index', [
            'filters' => $filters, 'total' => $total, 'active' => $active, 'archived' => $archived,
            'byType' => $byType, 'byCategory' => $byCategory, 'monthly' => $monthly,
            'records' => $records, 'categories' => Category::orderBy('name')->get(),
            'maxMonthly' => max(1, (int) $monthly->max('total')),
            'maxCategory' => max(1, (int) $byCategory->max('total')),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $this->validatedFilters($request);
        $records = $this->filteredQuery($filters)->with(['category', 'creator'])->orderBy('document_date')->orderBy('reference_number')->get();
        $filename = 'recordroom-report-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($records) {
            $output = fopen('php://output', 'w');
            // UTF-8 BOM helps Excel display Sinhala and other Unicode text correctly.
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Reference Number', 'Title', 'Document Type', 'Category', 'Department', 'Document Date', 'Status', 'Created By', 'Created At']);
            foreach ($records as $record) {
                $values = [
                    $record->reference_number, $record->title, ucfirst($record->document_type),
                    $record->category?->name ?? 'Uncategorised', $record->department,
                    $record->document_date?->format('Y-m-d'), ucfirst($record->status),
                    $record->creator?->name, $record->created_at?->format('Y-m-d H:i:s'),
                ];
                fputcsv($output, array_map([$this, 'safeCsvValue'], $values));
            }
            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function validatedFilters(Request $request): array
    {
        $data = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'type' => ['nullable', 'in:incoming,outgoing,internal,other'],
            'status' => ['nullable', 'in:active,archived'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'department' => ['nullable', 'string', 'max:120'],
        ]);
        return array_filter($data, fn ($value) => $value !== null && $value !== '');
    }

    private function filteredQuery(array $filters)
    {
        $query = Record::query();
        $this->applyFilters($query, $filters);
        return $query;
    }

    private function applyFilters($query, array $filters, bool $applyDate = true): void
    {
        if ($applyDate && isset($filters['from'])) $query->whereDate('document_date', '>=', $filters['from']);
        if ($applyDate && isset($filters['to'])) $query->whereDate('document_date', '<=', $filters['to']);
        if (isset($filters['type'])) $query->where('document_type', $filters['type']);
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        if (isset($filters['category_id'])) $query->where('category_id', $filters['category_id']);
        if (isset($filters['department'])) $query->where('department', 'like', '%' . $filters['department'] . '%');
    }

    private function safeCsvValue(mixed $value): string
    {
        $value = (string) ($value ?? '');
        return preg_match('/^[\s]*[=+@\-]/u', $value) ? "'" . $value : $value;
    }
}
