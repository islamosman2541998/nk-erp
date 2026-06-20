<?php

namespace App\Http\Controllers\Admin;

use App\Models\Transaction;
use App\Http\Requests\Admin\BulkUpdateTransactionDocumentsRequest;
use App\Models\TransactionDocument;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateTransactionDocumentRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\StoreTransactionDocumentRequest;
class TransactionDocumentController extends Controller
{
    public function update(UpdateTransactionDocumentRequest $request, TransactionDocument $transactionDocument)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            if ($transactionDocument->file_path) {
                Storage::disk('public')->delete($transactionDocument->file_path);
            }

            $data['file_path'] = $request->file('file')->store(
                'transactions/' . $transactionDocument->transaction_id . '/documents',
                'public'
            );

            $data['uploaded_by'] = auth()->id();
            $data['uploaded_at'] = now();

            if ($data['status'] === 'ناقص') {
                $data['status'] = 'تم الرفع';
            }
        }

        if (in_array($data['status'], ['مرفوض', 'تمت المراجعة', 'معتمد'])) {
            $data['reviewed_by'] = auth()->id();
            $data['reviewed_at'] = now();
        }

        $transactionDocument->update($data);

        return back()->with('success', 'تم تحديث المستند بنجاح');
    }
    public function bulkUpdate(BulkUpdateTransactionDocumentsRequest $request, Transaction $transaction)
    {
        foreach ($request->validated('documents') as $documentId => $documentData) {
            $document = $transaction->documents()->whereKey($documentId)->first();

            if (! $document) {
                continue;
            }

            if (! empty($documentData['clear_file'])) {
                if ($document->file_path) {
                    Storage::disk('public')->delete($document->file_path);
                }

                $document->update([
                    'file_path' => null,
                    'drive_link' => null,
                    'uploaded_by' => null,
                    'uploaded_at' => null,
                    'status' => 'ناقص',
                ]);

                continue;
            }

            $data = [
                'status' => $documentData['status'],
                'drive_link' => $documentData['drive_link'] ?? null,
                'notes' => $documentData['notes'] ?? null,
            ];

            if ($request->hasFile("documents.$documentId.file")) {
                if ($document->file_path) {
                    Storage::disk('public')->delete($document->file_path);
                }

                $data['file_path'] = $request->file("documents.$documentId.file")->store(
                    'transactions/' . $transaction->id . '/documents',
                    'public'
                );

                $data['uploaded_by'] = auth()->id();
                $data['uploaded_at'] = now();

                if ($data['status'] === 'ناقص') {
                    $data['status'] = 'تم الرفع';
                }
            }

            if (in_array($data['status'], ['مرفوض', 'تمت المراجعة', 'معتمد'])) {
                $data['reviewed_by'] = auth()->id();
                $data['reviewed_at'] = now();
            }

            $document->update($data);
        }

        return back()->with('success', 'تم حفظ تحديثات المستندات بنجاح');
    }
    public function store(StoreTransactionDocumentRequest $request, Transaction $transaction)
{
    $data = $request->validated();

    $documentData = [
        'name' => $data['name'],
        'status' => $data['status'] ?? 'ناقص',
        'drive_link' => $data['drive_link'] ?? null,
        'notes' => $data['notes'] ?? null,
    ];

    if ($request->hasFile('file')) {
        $documentData['file_path'] = $request->file('file')->store(
            'transactions/' . $transaction->id . '/documents',
            'public'
        );

        $documentData['uploaded_by'] = auth()->id();
        $documentData['uploaded_at'] = now();

        if (($documentData['status'] ?? 'ناقص') === 'ناقص') {
            $documentData['status'] = 'تم الرفع';
        }
    }

    $transaction->documents()->create($documentData);

    return back()->with('success', 'تم إضافة المستند بنجاح');
}
}