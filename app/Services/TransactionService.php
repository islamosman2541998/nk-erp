<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionService
{
    public function create(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $data['reference_number'] = $this->generateReferenceNumber();

            $data['status'] = $data['status']
                ?? app(SettingService::class)->get('default_transaction_status', 'تحت الإجراء');

            $data['created_by'] = Auth::id();

            $transaction = Transaction::create($data);

            $this->generateRequiredDocuments($transaction);

            return $transaction;
        });
    }

    private function generateReferenceNumber(): string
    {
        $year = now()->year;

        $prefix = app(SettingService::class)
            ->get('transaction_reference_prefix', 'NK-TRX');

        $digits = (int) app(SettingService::class)
            ->get('transaction_reference_digits', 4);

        $lastTransaction = Transaction::query()
            ->where('reference_number', 'like', "{$prefix}-{$year}-%")
            ->lockForUpdate()
            ->latest('id')
            ->first();

        $nextNumber = 1;

        if ($lastTransaction) {
            $lastNumber = (int) Str::afterLast($lastTransaction->reference_number, '-');
            $nextNumber = $lastNumber + 1;
        }

        return $prefix . '-' . $year . '-' . str_pad($nextNumber, $digits, '0', STR_PAD_LEFT);
    }

    private function generateRequiredDocuments(Transaction $transaction): void
    {
        $defaultDocumentStatus = app(SettingService::class)
            ->get('default_document_status', 'ناقص');

        $transactionType = TransactionType::query()
            ->with(['documentRequirements' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->findOrFail($transaction->transaction_type_id);

        foreach ($transactionType->documentRequirements as $requirement) {
            $transaction->documents()->firstOrCreate(
                [
                    'document_requirement_id' => $requirement->id,
                ],
                [
                    'name' => $requirement->name,
                    'status' => $defaultDocumentStatus,
                ]
            );
        }
    }

    public function update(Transaction $transaction, array $data): Transaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            $oldTransactionTypeId = $transaction->transaction_type_id;

            $data['updated_by'] = Auth::id();

            $transaction->update($data);

            if ((int) $oldTransactionTypeId !== (int) $transaction->transaction_type_id) {
                $this->generateRequiredDocuments($transaction);
            }

            return $transaction;
        });
    }

    public function delete(Transaction $transaction): void
    {
        $transaction->delete();
    }

    public function archive(Transaction $transaction, ?string $notes = null): Transaction
    {
        $transaction->update([
            'archived_at' => now(),
            'archived_by' => Auth::id(),
            'archive_notes' => $notes,
        ]);

        return $transaction;
    }

    public function unarchive(Transaction $transaction): Transaction
    {
        $transaction->update([
            'archived_at' => null,
            'archived_by' => null,
            'archive_notes' => null,
        ]);

        return $transaction;
    }
}