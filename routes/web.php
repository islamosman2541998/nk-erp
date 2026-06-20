<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TransactionTypeController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\TransactionDocumentController;
use App\Http\Controllers\Admin\TransactionTypeDocumentRequirementController;
use App\Http\Controllers\Admin\SettingController;

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
    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

        //  transaction types
        Route::resource('transaction-types', TransactionTypeController::class);

        Route::post('/transaction-types/{transactionType}/documents', [TransactionTypeDocumentRequirementController::class, 'store'])
            ->name('transaction-type-documents.store');

        Route::put('/transaction-type-documents/{documentRequirement}', [TransactionTypeDocumentRequirementController::class, 'update'])
            ->name('transaction-type-documents.update');

        Route::delete('/transaction-type-documents/{documentRequirement}', [TransactionTypeDocumentRequirementController::class, 'destroy'])
            ->name('transaction-type-documents.destroy');
        //  client
        Route::get('/clients/export/excel', [ClientController::class, 'export'])
            ->name('clients.export');
        Route::resource('clients', ClientController::class);
        //  transaction
        Route::get('/transactions/export/excel', [TransactionController::class, 'export'])
            ->name('transactions.export');
        Route::resource('transactions', TransactionController::class);
        //  transaction documents
        Route::patch('/transaction-documents/{transactionDocument}', [TransactionDocumentController::class, 'update'])
            ->name('transaction-documents.update');

        Route::patch('/transactions/{transaction}/documents', [TransactionDocumentController::class, 'bulkUpdate'])
            ->name('transactions.documents.bulk-update');

        Route::post('/transactions/{transaction}/documents', [TransactionDocumentController::class, 'store'])
            ->name('transactions.documents.store');
        Route::get('/transactions-archive', [TransactionController::class, 'archived'])
            ->name('transactions.archived');
        Route::get('/transactions-archive/export/excel', [TransactionController::class, 'exportArchived'])
            ->name('transactions.archived.export');

      
        Route::patch('/transactions/{transaction}/archive', [TransactionController::class, 'archive'])
            ->name('transactions.archive');

        Route::patch('/transactions/{transaction}/unarchive', [TransactionController::class, 'unarchive'])
            ->name('transactions.unarchive');

        Route::get('/settings', [SettingController::class, 'edit'])
            ->name('settings.edit');

        Route::put('/settings', [SettingController::class, 'update'])
            ->name('settings.update');
    });
});

require __DIR__ . '/auth.php';