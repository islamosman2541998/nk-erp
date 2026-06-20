<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function store(Request $request, Transaction $transaction)
    {
        abort_unless(
            auth()->user()->can('create expenses') &&
            $this->userCanAccessTransaction($transaction),
            403
        );

        $data = $request->validate([
            'expense_number' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'expense_date' => ['nullable', 'date'],
            'paid_to' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
            'receipt_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'drive_link' => ['nullable', 'url'],
            'notes' => ['nullable', 'string'],
        ]);

        $expenseData = [
            'transaction_id' => $transaction->id,
            'expense_number' => $data['expense_number'] ?? null,
            'category' => $data['category'] ?? null,
            'title' => $data['title'],
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? app(\App\Services\SettingService::class)->get('default_currency', 'SAR'),
            'expense_date' => $data['expense_date'] ?? null,
            'paid_to' => $data['paid_to'] ?? null,
            'payment_method' => $data['payment_method'] ?? null,
            'status' => $data['status'],
            'drive_link' => $data['drive_link'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => auth()->id(),
        ];

        if ($request->hasFile('receipt_file')) {
            $expenseData['receipt_file_path'] = $request->file('receipt_file')->store(
                'transactions/' . $transaction->id . '/expenses',
                'public'
            );
        }

        $transaction->expenses()->create($expenseData);

        return back()->with('success', 'تم إضافة المصروف بنجاح');
    }

    public function update(Request $request, Expense $expense)
    {
        $expense->load('transaction');

        abort_unless(
            auth()->user()->can('edit expenses') &&
            $this->userCanAccessTransaction($expense->transaction),
            403
        );

        $data = $request->validate([
            'expense_number' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'expense_date' => ['nullable', 'date'],
            'paid_to' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
            'receipt_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'drive_link' => ['nullable', 'url'],
            'notes' => ['nullable', 'string'],
            'clear_file' => ['nullable', 'boolean'],
        ]);

        $expenseData = [
            'expense_number' => $data['expense_number'] ?? null,
            'category' => $data['category'] ?? null,
            'title' => $data['title'],
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? app(\App\Services\SettingService::class)->get('default_currency', 'SAR'),
            'expense_date' => $data['expense_date'] ?? null,
            'paid_to' => $data['paid_to'] ?? null,
            'payment_method' => $data['payment_method'] ?? null,
            'status' => $data['status'],
            'drive_link' => $data['drive_link'] ?? null,
            'notes' => $data['notes'] ?? null,
            'updated_by' => auth()->id(),
        ];

        if ($request->boolean('clear_file')) {
            if ($expense->receipt_file_path) {
                Storage::disk('public')->delete($expense->receipt_file_path);
            }

            $expenseData['receipt_file_path'] = null;
        }

        if ($request->hasFile('receipt_file')) {
            if ($expense->receipt_file_path) {
                Storage::disk('public')->delete($expense->receipt_file_path);
            }

            $expenseData['receipt_file_path'] = $request->file('receipt_file')->store(
                'transactions/' . $expense->transaction_id . '/expenses',
                'public'
            );
        }

        $expense->update($expenseData);

        return back()->with('success', 'تم تعديل المصروف بنجاح');
    }

    public function destroy(Expense $expense)
    {
        $expense->load('transaction');

        abort_unless(
            auth()->user()->can('delete expenses') &&
            $this->userCanAccessTransaction($expense->transaction),
            403
        );

        if ($expense->receipt_file_path) {
            Storage::disk('public')->delete($expense->receipt_file_path);
        }

        $expense->delete();

        return back()->with('success', 'تم حذف المصروف بنجاح');
    }

    private function userCanAccessTransaction(Transaction $transaction): bool
    {
        $user = auth()->user();

        if ($user->can('view transactions')) {
            return true;
        }

        if (!$user->can('view assigned transactions')) {
            return false;
        }

        return in_array($user->id, [
            $transaction->assigned_to,
            $transaction->technical_manager_id,
            $transaction->coordinator_id,
            $transaction->financial_user_id,
        ]);
    }
}