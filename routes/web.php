<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\RecordAttachmentController;
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
    Route::get('/records/create', [RecordController::class, 'create'])->middleware('role:admin,manager,staff')->name('records.create');
    Route::post('/records', [RecordController::class, 'store'])->middleware('role:admin,manager,staff')->name('records.store');
    Route::get('/records/{record}', [RecordController::class, 'show'])->name('records.show');
    Route::get('/records/{record}/edit', [RecordController::class, 'edit'])->middleware('role:admin,manager,staff')->name('records.edit');
    Route::put('/records/{record}', [RecordController::class, 'update'])->middleware('role:admin,manager,staff')->name('records.update');
    Route::patch('/records/{record}/archive', [RecordController::class, 'archive'])->middleware('role:admin,manager')->name('records.archive');
    Route::patch('/records/{record}/restore', [RecordController::class, 'restore'])->middleware('role:admin,manager')->name('records.restore');
    Route::post('/records/{record}/attachments', [RecordAttachmentController::class, 'store'])
        ->middleware('role:admin,manager,staff')->name('records.attachments.store');
    Route::get('/records/{record}/attachments/{attachment}/download', [RecordAttachmentController::class, 'download'])
        ->name('records.attachments.download');
    Route::delete('/records/{record}/attachments/{attachment}', [RecordAttachmentController::class, 'destroy'])
        ->middleware('role:admin,manager,staff')->name('records.attachments.destroy');
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });
    Route::middleware('role:admin')->group(function () {
        Route::get('/activity-logs', [LogController::class, 'index'])->name('logs.index');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    });

});
