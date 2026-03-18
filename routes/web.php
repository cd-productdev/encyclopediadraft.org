<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\ArticleWebController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Root route - redirect based on authentication
Route::get('/', function () {
    return auth()->check() ? redirect()->route('articles.index') : redirect()->route('login');
});

// Dashboard route - redirect to articles for regular users
Route::get('/dashboard', function () {
    if (auth()->user()?->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('articles.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public article show route (no authentication required)
Route::get('/articles/{article}', [ArticleWebController::class, 'show'])->name('articles.show');

// Article routes (for authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/articles', [ArticleWebController::class, 'index'])->name('articles.index');
    Route::get('/articles/create', [ArticleWebController::class, 'create'])->name('articles.create');
    Route::post('/articles', [ArticleWebController::class, 'store'])->name('articles.store');
    
    // Article actions (must be before edit route)
    Route::post('/articles/{article}/approve', [ArticleWebController::class, 'approve'])->name('articles.approve');
    Route::post('/articles/{article}/reject', [ArticleWebController::class, 'reject'])->name('articles.reject');
    
    Route::get('/articles/{article}/edit', [ArticleWebController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{article}', [ArticleWebController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{article}', [ArticleWebController::class, 'destroy'])->name('articles.destroy');
    
    // Image upload for CKEditor
    Route::post('/articles/upload-image', [ArticleWebController::class, 'uploadImage'])->name('articles.upload-image');
});

// Admin routes
Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Custom user routes (must be before resource)
    Route::post('users/{user}/change-password', [UserController::class, 'changePassword'])
        ->withTrashed()
        ->name('users.changePassword');
    Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.forceDelete');
    
    // Resource routes
    Route::resource('users', UserController::class);
    
    // Article management routes
    Route::get('/articles', [\App\Http\Controllers\Admin\ArticleController::class, 'index'])->name('articles.index');
    Route::post('/articles/{article}/approve', [\App\Http\Controllers\Admin\ArticleController::class, 'approve'])->name('articles.approve');
    Route::post('/articles/{article}/reject', [\App\Http\Controllers\Admin\ArticleController::class, 'reject'])->name('articles.reject');
    Route::delete('/articles/{article}', [\App\Http\Controllers\Admin\ArticleController::class, 'destroy'])->name('articles.destroy');
    Route::post('/articles/{id}/restore', [\App\Http\Controllers\Admin\ArticleController::class, 'restore'])->name('articles.restore');
    Route::delete('/articles/{id}/force-delete', [\App\Http\Controllers\Admin\ArticleController::class, 'forceDelete'])->name('articles.forceDelete');
});

require __DIR__.'/auth.php';
