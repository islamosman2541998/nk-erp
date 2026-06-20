<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommissionController extends Controller
{
    public function store(Request $request, Transaction $transaction)
    {
        abort_unless(
            auth()->user()->can('create commissions') &&
            $this->userCanAccessTransaction($transaction),
            403
        );

        $data = $this->validateCommission($request);

        $commissionData = $this->prepareCommissionData($data, $transaction);
        $commissionData['created_by'] = auth()->id();

        if ($request->hasFile('proof_file')) {
            $commissionData['proof_file_path'] = $request->file('proof_file')->store(
                'transactions/' . $transaction->id . '/commissions',
                'public'
            );
        }

        $transaction->commissions()->create($commissionData);

        return back()->with('success', 'تم إضافة العمولة بنجاح');
    }

    public function update(Request $request, Commission $commission)
    {
        $commission->load('transaction');

        abort_unless(
            auth()->user()->can('edit commissions') &&
            $this->userCanAccessTransaction($commission->transaction),
            403
        );

        $data = $this->validateCommission($request, true);

        $commissionData = $this->prepareCommissionData($data, $commission->transaction);
        $commissionData['updated_by'] = auth()->id();

        if ($request->boolean('clear_file')) {
            if ($commission->proof_file_path) {
                Storage::disk('public')->delete($commission->proof_file_path);
            }

            $commissionData['proof_file_path'] = null;
        }

        if ($request->hasFile('proof_file')) {
            if ($commission->proof_file_path) {
                Storage::disk('public')->delete($commission->proof_file_path);
            }

            $commissionData['proof_file_path'] = $request->file('proof_file')->store(
                'transactions/' . $commission->transaction_id . '/commissions',
                'public'
            );
        }

        $commission->update($commissionData);

        return back()->with('success', 'تم تعديل العمولة بنجاح');
    }

    public function destroy(Commission $commission)
    {
        $commission->load('transaction');

        abort_unless(
            auth()->user()->can('delete commissions') &&
            $this->userCanAccessTransaction($commission->transaction),
            403
        );

        if ($commission->proof_file_path) {
            Storage::disk('public')->delete($commission->proof_file_path);
        }

        $commission->delete();

        return back()->with('success', 'تم حذف العمولة بنجاح');
    }

    private function validateCommission(Request $request, bool $isUpdate = false): array
    {
        return $request->validate([
            'commission_number' => ['nullable', 'string', 'max:255'],

            'commission_category' => ['required', 'string', 'in:داخلية,خارجية'],

            'recipient_user_id' => ['nullable', 'exists:users,id'],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'recipient_phone' => ['nullable', 'string', 'max:255'],
            'recipient_email' => ['nullable', 'email', 'max:255'],

            'calculation_type' => ['required', 'string', 'in:نسبة,مبلغ ثابت'],
            'base_type' => ['required', 'string', 'in:قيمة العقد,إجمالي المدفوع,صافي الربح'],

            'percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'fixed_amount' => ['nullable', 'numeric', 'min:0'],

            'currency' => ['nullable', 'string', 'max:10'],

            'due_date' => ['nullable', 'date'],
            'payment_date' => ['nullable', 'date'],

            'status' => ['required', 'string', 'in:مستحقة,مدفوعة,ملغية'],

            'proof_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'drive_link' => ['nullable', 'url'],

            'notes' => ['nullable', 'string'],

            'clear_file' => ['nullable', 'boolean'],
        ]);
    }

    private function prepareCommissionData(array $data, Transaction $transaction): array
    {
        $baseAmount = $this->getBaseAmount($transaction, $data['base_type']);

        $calculatedAmount = 0;

        if ($data['calculation_type'] === 'نسبة') {
            $percentage = (float) ($data['percentage'] ?? 0);
            $calculatedAmount = ($baseAmount * $percentage) / 100;
        }

        if ($data['calculation_type'] === 'مبلغ ثابت') {
            $calculatedAmount = (float) ($data['fixed_amount'] ?? 0);
        }

        return [
            'commission_number' => $data['commission_number'] ?? null,

            'commission_category' => $data['commission_category'],

            'recipient_user_id' => $data['commission_category'] === 'داخلية'
                ? ($data['recipient_user_id'] ?? null)
                : null,

            'recipient_name' => $data['commission_category'] === 'خارجية'
                ? ($data['recipient_name'] ?? null)
                : null,

            'recipient_phone' => $data['commission_category'] === 'خارجية'
                ? ($data['recipient_phone'] ?? null)
                : null,

            'recipient_email' => $data['commission_category'] === 'خارجية'
                ? ($data['recipient_email'] ?? null)
                : null,

            'calculation_type' => $data['calculation_type'],
            'base_type' => $data['base_type'],

            'percentage' => $data['calculation_type'] === 'نسبة'
                ? ($data['percentage'] ?? null)
                : null,

            'fixed_amount' => $data['calculation_type'] === 'مبلغ ثابت'
                ? ($data['fixed_amount'] ?? null)
                : null,

            'calculated_amount' => $calculatedAmount,

            'currency' => $data['currency'] ?? app(\App\Services\SettingService::class)->get('default_currency', 'SAR'),

            'due_date' => $data['due_date'] ?? null,
            'payment_date' => $data['payment_date'] ?? null,

            'status' => $data['status'],

            'drive_link' => $data['drive_link'] ?? null,
            'notes' => $data['notes'] ?? null,
        ];
    }

    private function getBaseAmount(Transaction $transaction, string $baseType): float
    {
        $transaction->loadMissing(['contract', 'payments', 'expenses']);

        $contractValue = (float) ($transaction->contract?->contract_value ?? 0);

        $paidTotal = (float) $transaction->payments
            ->where('status', 'مدفوعة')
            ->sum('amount');

        $expensesTotal = (float) $transaction->expenses
            ->where('status', '!=', 'ملغي')
            ->sum('amount');

        return match ($baseType) {
            'قيمة العقد' => $contractValue,
            'إجمالي المدفوع' => $paidTotal,
            'صافي الربح' => max($paidTotal - $expensesTotal, 0),
            default => 0,
        };
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