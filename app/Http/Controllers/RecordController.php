<?php

namespace App\Http\Controllers;

use App\Support\ActivityLogger;
use App\Models\User;
use App\Notifications\RecordCreatedNotification;

use App\Models\Category;
use App\Models\Record;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RecordController extends Controller
{
    public function index(Request $request): View
    {
        $query = Record::with(['category', 'creator'])->latest();

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($type = $request->query('type')) {
            $query->where('document_type', $type);
        }

        $status = $request->query('status', 'active');
        if (in_array($status, ['active', 'archived'], true)) {
            $query->where('status', $status);
        }

        return view('records.index', [
            'records' => $query->paginate(10)->withQueryString(),
            'search' => $request->query('q', ''),
            'type' => $request->query('type', ''),
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('records.create', ['categories' => Category::where('is_active', true)->orderBy('name')->get(), 'record' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['reference_number'] = $this->newReferenceNumber();
        $data['created_by'] = $request->user()->id;

        $record = Record::create($data);
        ActivityLogger::log('record.created', 'Created record '.$record->reference_number, $record);
        User::where('is_active', true)->whereIn('role', ['admin', 'manager'])->where('id', '!=', $request->user()->id)->get()
            ->each(fn (User $user) => $user->notify(new RecordCreatedNotification($record)));
        $request->user()->notify(new RecordCreatedNotification($record));

        return redirect()->route('records.index')->with('success', 'Record created successfully.');
    }

    public function show(Record $record): View
    {
        $record->load(['category', 'creator']);
        return view('records.show', compact('record'));
    }

    public function edit(Record $record): View
    {
        abort_if($record->status === 'archived', 404);
        return view('records.edit', [
            'record' => $record,
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Record $record): RedirectResponse
    {
        abort_if($record->status === 'archived', 404);
        $record->update($this->validatedData($request));
        ActivityLogger::log('record.updated', 'Updated record '.$record->reference_number, $record);

        return redirect()->route('records.show', $record)->with('success', 'Record updated successfully.');
    }

    public function archive(Record $record): RedirectResponse
    {
        if ($record->status !== 'archived') {
            $record->update(['status' => 'archived', 'archived_at' => now()]);
            ActivityLogger::log('record.archived', 'Archived record '.$record->reference_number, $record);
        }

        return redirect()->route('records.index', ['status' => 'archived'])->with('success', 'Record archived.');
    }

    public function restore(Record $record): RedirectResponse
    {
        if ($record->status === 'archived') {
            $record->update(['status' => 'active', 'archived_at' => null]);
            ActivityLogger::log('record.restored', 'Restored record '.$record->reference_number, $record);
        }

        return redirect()->route('records.show', $record)->with('success', 'Record restored.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:10000'],
            'document_type' => ['required', Rule::in(['incoming', 'outgoing', 'internal', 'other'])],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'department' => ['nullable', 'string', 'max:120'],
            'document_date' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date'],
        ]);
    }

    private function newReferenceNumber(): string
    {
        do {
            $reference = 'RR-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));
        } while (Record::where('reference_number', $reference)->exists());

        return $reference;
    }
}
