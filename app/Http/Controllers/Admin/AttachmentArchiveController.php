<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ArrayExport;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\TransactionDocument;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AttachmentArchiveController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(
            auth()->user()->can('view archive') ||
            auth()->user()->can('view attachments'),
            403
        );

        $documents = $this->documentsQuery($request)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $clients = Client::query()
            ->orderBy('name')
            ->get();

        $transactionTypes = TransactionType::query()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $statuses = $this->documentStatuses();

        return view('admin.archive.attachments', compact(
            'documents',
            'clients',
            'transactionTypes',
            'statuses'
        ));
    }

    public function export(Request $request)
    {
        abort_unless(
            auth()->user()->can('view archive') ||
            auth()->user()->can('view attachments'),
            403
        );

        $documents = $this->documentsQuery($request)
            ->latest()
            ->get();

        $headings = [
            'اسم المستند',
            'رقم المعاملة',
            'العميل',
            'نوع المعاملة',
            'اسم المشروع',
            'حالة المستند',
            'رابط الملف',
            'رابط Drive',
            'رفع بواسطة',
            'تاريخ الرفع',
            'مراجعة بواسطة',
            'تاريخ المراجعة',
            'حالة المعاملة',
            'مؤرشفة؟',
            'تاريخ الأرشفة',
            'ملاحظات',
        ];

        $rows = $documents->map(function ($document) {
            $transaction = $document->transaction;

            return [
                $document->name,
                (string) $transaction?->reference_number,
                $transaction?->client?->name,
                $transaction?->transactionType?->name,
                $transaction?->project_name,
                $document->status,
                $document->file_path ? asset('storage/' . $document->file_path) : null,
                $document->drive_link,
                $document->uploadedBy?->name,
                $document->uploaded_at?->format('Y-m-d H:i'),
                $document->reviewedBy?->name,
                $document->reviewed_at?->format('Y-m-d H:i'),
                $transaction?->status,
                $transaction?->archived_at ? 'نعم' : 'لا',
                $transaction?->archived_at?->format('Y-m-d H:i'),
                $document->notes,
            ];
        })->toArray();

        return Excel::download(
            new ArrayExport(
                $headings,
                $rows,
                'أرشيف المرفقات',
                [
                    'B' => NumberFormat::FORMAT_TEXT,
                    'G' => NumberFormat::FORMAT_TEXT,
                    'H' => NumberFormat::FORMAT_TEXT,
                ]
            ),
            'attachments-archive-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    private function documentsQuery(Request $request)
    {
        return TransactionDocument::query()
            ->with([
                'transaction.client',
                'transaction.transactionType',
                'uploadedBy',
                'reviewedBy',
            ])
            ->whereHas('transaction')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('transaction', function ($transactionQuery) use ($search) {
                            $transactionQuery->where('reference_number', 'like', "%{$search}%")
                                ->orWhere('project_name', 'like', "%{$search}%")
                                ->orWhere('permit_number', 'like', "%{$search}%")
                                ->orWhereHas('client', function ($clientQuery) use ($search) {
                                    $clientQuery->where('name', 'like', "%{$search}%")
                                        ->orWhere('facility_name', 'like', "%{$search}%");
                                });
                        });
                });
            })
            ->when($request->filled('client_id'), function ($query) use ($request) {
                $query->whereHas('transaction', function ($transactionQuery) use ($request) {
                    $transactionQuery->where('client_id', $request->client_id);
                });
            })
            ->when($request->filled('transaction_type_id'), function ($query) use ($request) {
                $query->whereHas('transaction', function ($transactionQuery) use ($request) {
                    $transactionQuery->where('transaction_type_id', $request->transaction_type_id);
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('attachment_type'), function ($query) use ($request) {
                match ($request->attachment_type) {
                    'file' => $query->whereNotNull('file_path'),
                    'drive' => $query->whereNotNull('drive_link'),
                    'missing' => $query->whereNull('file_path')->whereNull('drive_link'),
                    default => null,
                };
            })
            ->when($request->filled('archive_status'), function ($query) use ($request) {
                if ($request->archive_status === 'archived') {
                    $query->whereHas('transaction', function ($transactionQuery) {
                        $transactionQuery->archived();
                    });
                }

                if ($request->archive_status === 'active') {
                    $query->whereHas('transaction', function ($transactionQuery) {
                        $transactionQuery->active();
                    });
                }
            })
            ->when($request->filled('uploaded_from'), function ($query) use ($request) {
                $query->whereDate('uploaded_at', '>=', $request->uploaded_from);
            })
            ->when($request->filled('uploaded_to'), function ($query) use ($request) {
                $query->whereDate('uploaded_at', '<=', $request->uploaded_to);
            });
    }

    private function documentStatuses(): array
    {
        return [
            'مطلوب',
            'ناقص',
            'تم الرفع',
            'مرفوض',
            'تمت المراجعة',
            'معتمد',
        ];
    }
}