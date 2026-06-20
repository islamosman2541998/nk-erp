<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\RoleManagementController;

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\TransactionDocumentController;

use App\Http\Controllers\Admin\TransactionTypeController;
use App\Http\Controllers\Admin\TransactionTypeDocumentRequirementController;

use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\CommissionController;

use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TransactionReportController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\AttachmentArchiveController;

use App\Http\Controllers\Admin\SettingController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Users & Roles
        |--------------------------------------------------------------------------
        */
        Route::resource('users', UserManagementController::class)
            ->except(['show']);

        Route::resource('roles', RoleManagementController::class)
            ->except(['show']);

        /*
        |--------------------------------------------------------------------------
        | Audit Logs
        |--------------------------------------------------------------------------
        */
        Route::get('/audit-logs', [AuditLogController::class, 'index'])
            ->name('audit-logs.index');

        /*
        |--------------------------------------------------------------------------
        | Clients
        |--------------------------------------------------------------------------
        */
        Route::get('/clients/export/excel', [ClientController::class, 'export'])
            ->name('clients.export');

        Route::resource('clients', ClientController::class);

        /*
        |--------------------------------------------------------------------------
        | Transaction Types
        |--------------------------------------------------------------------------
        */
        Route::resource('transaction-types', TransactionTypeController::class);

        Route::post('/transaction-types/{transactionType}/documents', [TransactionTypeDocumentRequirementController::class, 'store'])
            ->name('transaction-type-documents.store');

        Route::put('/transaction-type-documents/{documentRequirement}', [TransactionTypeDocumentRequirementController::class, 'update'])
            ->name('transaction-type-documents.update');

        Route::delete('/transaction-type-documents/{documentRequirement}', [TransactionTypeDocumentRequirementController::class, 'destroy'])
            ->name('transaction-type-documents.destroy');

        /*
        |--------------------------------------------------------------------------
        | Transactions Archive
        |--------------------------------------------------------------------------
        */
        Route::get('/transactions-archive', [TransactionController::class, 'archived'])
            ->name('transactions.archived');

        Route::get('/transactions-archive/export/excel', [TransactionController::class, 'exportArchived'])
            ->name('transactions.archived.export');

        Route::patch('/transactions/{transaction}/archive', [TransactionController::class, 'archive'])
            ->name('transactions.archive');

        Route::patch('/transactions/{transaction}/unarchive', [TransactionController::class, 'unarchive'])
            ->name('transactions.unarchive');

        /*
        |--------------------------------------------------------------------------
        | Transactions
        |--------------------------------------------------------------------------
        */
        Route::get('/transactions/export/excel', [TransactionController::class, 'export'])
            ->name('transactions.export');

        Route::resource('transactions', TransactionController::class);

        /*
        |--------------------------------------------------------------------------
        | Transaction Documents
        |--------------------------------------------------------------------------
        */
        Route::patch('/transaction-documents/{transactionDocument}', [TransactionDocumentController::class, 'update'])
            ->name('transaction-documents.update');

        Route::patch('/transactions/{transaction}/documents', [TransactionDocumentController::class, 'bulkUpdate'])
            ->name('transactions.documents.bulk-update');

        Route::post('/transactions/{transaction}/documents', [TransactionDocumentController::class, 'store'])
            ->name('transactions.documents.store');

        /*
|--------------------------------------------------------------------------
| Contracts
|--------------------------------------------------------------------------
*/
Route::get('/contracts', [ContractController::class, 'index'])
    ->name('contracts.index');

Route::get('/contracts/export/excel', [ContractController::class, 'export'])
    ->name('contracts.export');

Route::post('/transactions/{transaction}/contract', [ContractController::class, 'store'])
    ->name('transactions.contract.store');

Route::put('/contracts/{contract}', [ContractController::class, 'update'])
    ->name('contracts.update');

Route::delete('/contracts/{contract}', [ContractController::class, 'destroy'])
    ->name('contracts.destroy');

        /*
        |--------------------------------------------------------------------------
        | Payments
        |--------------------------------------------------------------------------
        */
        Route::post('/transactions/{transaction}/payments', [PaymentController::class, 'store'])
            ->name('transactions.payments.store');

        Route::put('/payments/{payment}', [PaymentController::class, 'update'])
            ->name('payments.update');

        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])
            ->name('payments.destroy');

        /*
        |--------------------------------------------------------------------------
        | Expenses
        |--------------------------------------------------------------------------
        */
        Route::post('/transactions/{transaction}/expenses', [ExpenseController::class, 'store'])
            ->name('transactions.expenses.store');

        Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])
            ->name('expenses.update');

        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])
            ->name('expenses.destroy');

        /*
        |--------------------------------------------------------------------------
        | Commissions
        |--------------------------------------------------------------------------
        */
        Route::post('/transactions/{transaction}/commissions', [CommissionController::class, 'store'])
            ->name('transactions.commissions.store');

        Route::put('/commissions/{commission}', [CommissionController::class, 'update'])
            ->name('commissions.update');

        Route::delete('/commissions/{commission}', [CommissionController::class, 'destroy'])
            ->name('commissions.destroy');

        /*
        |--------------------------------------------------------------------------
        | Reports
        |--------------------------------------------------------------------------
        */
        Route::get('/reports/financial', [ReportController::class, 'financial'])
            ->name('reports.financial');

        Route::get('/reports/financial/export', [ReportController::class, 'financialExport'])
            ->name('reports.financial.export');

        Route::get('/reports/transactions', [TransactionReportController::class, 'index'])
            ->name('reports.transactions');

        Route::get('/reports/transactions/export', [TransactionReportController::class, 'export'])
            ->name('reports.transactions.export');

        /*
        |--------------------------------------------------------------------------
        | Attachments Archive
        |--------------------------------------------------------------------------
        */
        Route::get('/archive/attachments', [AttachmentArchiveController::class, 'index'])
            ->name('archive.attachments');

        Route::get('/archive/attachments/export', [AttachmentArchiveController::class, 'export'])
            ->name('archive.attachments.export');

        /*
        |--------------------------------------------------------------------------
        | Settings
        |--------------------------------------------------------------------------
        */
        Route::get('/settings', [SettingController::class, 'edit'])
            ->name('settings.edit');

        Route::put('/settings', [SettingController::class, 'update'])
            ->name('settings.update');
    });

require __DIR__ . '/auth.php';