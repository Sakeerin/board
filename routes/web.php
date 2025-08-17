<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\BoardListController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Settings
    Route::get('/boards/{board}/settings', [BoardController::class, 'settings'])->name('boards.settings');
    Route::post('/boards/{board}/settings', [BoardController::class, 'updateSettings'])->name('boards.settings.update');
    Route::post('/boards/{board}/invite', [BoardController::class, 'invite'])->name('boards.invite');
    Route::delete('/boards/{board}/members/{user}', [BoardController::class, 'removeMember'])->name('boards.members.remove');

    // Borads 
    Route::resource('boards', BoardController::class)->only(['index','store','show','destroy']);
    Route::post('/boards/{board}/lists', [BoardListController::class, 'store'])->name('lists.store');
    Route::post('/lists/{list}/rename', [BoardListController::class, 'rename'])->name('lists.rename');
    Route::post('/boards/{board}/lists/reorder', [BoardListController::class, 'reorder'])->name('lists.reorder');

    Route::post('/lists/{list}/cards', [CardController::class, 'store'])->name('cards.store');
    Route::post('/boards/{board}/cards/reorder', [CardController::class, 'reorder'])->name('cards.reorder');
    Route::post('/cards/{card}', [CardController::class, 'update'])->name('cards.update');
    Route::delete('/cards/{card}', [CardController::class, 'destroy'])->name('cards.destroy');

    Route::post('/cards/{card}/comments', [CommentController::class, 'store'])->name('comments.store');

    Route::post('/boards/{board}/labels', [LabelController::class, 'store'])->name('labels.store');
    Route::post('/boards/{board}/cards/{card}/labels/{label}', [LabelController::class, 'attach'])->name('labels.attach');
    Route::delete('/boards/{board}/cards/{card}/labels/{label}', [LabelController::class, 'detach'])->name('labels.detach');
});

require __DIR__.'/auth.php';
