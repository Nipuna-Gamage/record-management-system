<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RecordController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
});
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('dashboard', [
            'totalRecords' => \App\Models\Record::where('status', 'active')->count(),
            'archivedRecords' => \App\Models\Record::where('status', 'archived')->count(),
            'recentRecords' => \App\Models\Record::with('category')->latest()->take(5)->get(),
        ]);
    })->name('dashboard');
    Route::get('/records', [RecordController::class, 'index'])->name('records.index');
    Route::get('/records/create', [RecordController::class, 'create'])->name('records.create');
    Route::post('/records', [RecordController::class, 'store'])->name('records.store');
    Route::get('/records/{record}', [RecordController::class, 'show'])->name('records.show');
    Route::get('/records/{record}/edit', [RecordController::class, 'edit'])->name('records.edit');
    Route::put('/records/{record}', [RecordController::class, 'update'])->name('records.update');
    Route::patch('/records/{record}/archive', [RecordController::class, 'archive'])->name('records.archive');
    Route::patch('/records/{record}/restore', [RecordController::class, 'restore'])->name('records.restore');
});
