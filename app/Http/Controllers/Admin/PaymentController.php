<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function store(Request $request, Transaction $transaction)
    {
        abort_unless(
            auth()->user()->can('create payments') &&
            $this->userCanAccessTransaction($transaction),
            403
        );

        $data = $request->validate([
            'payment_number' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'due_date' => ['nullable', 'date'],
            'payment_date' => ['nullable', 'date'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
            'proof_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'drive_link' => ['nullable', 'url'],
            'notes' => ['nullable', 'string'],
        ]);

        $paymentData = [
            'transaction_id' => $transaction->id,
            'contract_id' => $transaction->contract?->id,
            'payment_number' => $data['payment_number'] ?? null,
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? app(\App\Services\SettingService::class)->get('default_currency', 'SAR'),
            'due_date' => $data['due_date'] ?? null,
            'payment_date' => $data['payment_date'] ?? null,
            'payment_method' => $data['payment_method'] ?? null,
            'status' => $data['status'],
            'drive_link' => $data['drive_link'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => auth()->id(),
        ];

        if ($request->hasFile('proof_file')) {
            $paymentData['proof_file_path'] = $request->file('proof_file')->store(
                'transactions/' . $transaction->id . '/payments',
                'public'
            );

            if ($paymentData['status'] === 'مستحقة') {
                $paymentData['status'] = 'مدفوعة';
            }

            if (empty($paymentData['payment_date'])) {
                $paymentData['payment_date'] = now()->toDateString();
            }
        }

        $transaction->payments()->create($paymentData);

        return back()->with('success', 'تم إضافة الدفعة بنجاح');
    }

    public function update(Request $request, Payment $payment)
    {
        $payment->load('transaction');

        abort_unless(
            auth()->user()->can('edit payments') &&
            $this->userCanAccessTransaction($payment->transaction),
            403
        );

        $data = $request->validate([
            'payment_number' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'due_date' => ['nullable', 'date'],
            'payment_date' => ['nullable', 'date'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
            'proof_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'drive_link' => ['nullable', 'url'],
            'notes' => ['nullable', 'string'],
            'clear_file' => ['nullable', 'boolean'],
        ]);

        $paymentData = [
            'payment_number' => $data['payment_number'] ?? null,
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? app(\App\Services\SettingService::class)->get('default_currency', 'SAR'),
            'due_date' => $data['due_date'] ?? null,
            'payment_date' => $data['payment_date'] ?? null,
            'payment_method' => $data['payment_method'] ?? null,
            'status' => $data['status'],
            'drive_link' => $data['drive_link'] ?? null,
            'notes' => $data['notes'] ?? null,
            'updated_by' => auth()->id(),
        ];

        if ($request->boolean('clear_file')) {
            if ($payment->proof_file_path) {
                Storage::disk('public')->delete($payment->proof_file_path);
            }

            $paymentData['proof_file_path'] = null;
        }

        if ($request->hasFile('proof_file')) {
            if ($payment->proof_file_path) {
                Storage::disk('public')->delete($payment->proof_file_path);
            }

            $paymentData['proof_file_path'] = $request->file('proof_file')->store(
                'transactions/' . $payment->transaction_id . '/payments',
                'public'
            );

            if ($paymentData['status'] === 'مستحقة') {
                $paymentData['status'] = 'مدفوعة';
            }

            if (empty($paymentData['payment_date'])) {
                $paymentData['payment_date'] = now()->toDateString();
            }
        }

        $payment->update($paymentData);

        return back()->with('success', 'تم تعديل الدفعة بنجاح');
    }

    public function destroy(Payment $payment)
    {
        $payment->load('transaction');

        abort_unless(
            auth()->user()->can('delete payments') &&
            $this->userCanAccessTransaction($payment->transaction),
            403
        );

        if ($payment->proof_file_path) {
            Storage::disk('public')->delete($payment->proof_file_path);
        }

        $payment->delete();

        return back()->with('success', 'تم حذف الدفعة بنجاح');
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