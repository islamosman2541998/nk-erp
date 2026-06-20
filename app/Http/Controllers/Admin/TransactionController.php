<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ArrayExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTransactionRequest;
use App\Http\Requests\Admin\UpdateTransactionRequest;
use App\Models\Client;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\User;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService
    ) {}

    public function index(Request $request)
    {
        abort_unless(
            auth()->user()->can('view transactions') ||
                auth()->user()->can('view assigned transactions'),
            403
        );

        $transactions = $this->transactionsQuery($request)
            ->active()
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.transactions.index', compact('transactions'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('create transactions'), 403);

        $clients = Client::query()
            ->orderBy('name')
            ->get();

        $transactionTypes = TransactionType::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $users = User::query()
            ->orderBy('name')
            ->get();

        return view('admin.transactions.create', compact(
            'clients',
            'transactionTypes',
            'users'
        ));
    }

    public function store(StoreTransactionRequest $request)
    {
        abort_unless(auth()->user()->can('create transactions'), 403);

        $transaction = $this->transactionService->create($request->validated());

        return redirect()
            ->route('admin.transactions.show', $transaction)
            ->with('success', 'تم إنشاء المعاملة بنجاح وتوليد المستندات المطلوبة تلقائيًا');
    }

    public function show(Transaction $transaction)
    {
        abort_unless($this->userCanAccessTransaction($transaction), 403);

        $transaction->load([
            'client',
            'transactionType',
            'assignedUser',
            'technicalManager',
            'coordinator',
            'financialUser',
            'documents',
            'contract',
            'payments' => function ($query) {
                $query->latest();
            },
            'expenses' => function ($query) {
                $query->latest();
            },
            'commissions' => function ($query) {
                $query->with('recipientUser')->latest();
            },
        ]);

        $users = User::query()
            ->orderBy('name')
            ->get();

        return view('admin.transactions.show', compact('transaction', 'users'));
    }

    public function edit(Transaction $transaction)
    {
        abort_unless(
            auth()->user()->can('edit transactions') &&
                $this->userCanAccessTransaction($transaction),
            403
        );

        $transaction->load([
            'client',
            'transactionType',
            'assignedUser',
            'technicalManager',
            'coordinator',
            'financialUser',
        ]);

        $clients = Client::query()
            ->orderBy('name')
            ->get();

        $transactionTypes = TransactionType::query()
            ->whereNull('parent_id')
            ->where(function ($query) use ($transaction) {
                $query->where('is_active', true)
                    ->orWhere('id', $transaction->transaction_type_id);
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $users = User::query()
            ->orderBy('name')
            ->get();

        return view('admin.transactions.edit', compact(
            'transaction',
            'clients',
            'transactionTypes',
            'users'
        ));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        abort_unless(
            auth()->user()->can('edit transactions') &&
                $this->userCanAccessTransaction($transaction),
            403
        );

        $this->transactionService->update($transaction, $request->validated());

        return redirect()
            ->route('admin.transactions.show', $transaction)
            ->with('success', 'تم تعديل بيانات المعاملة بنجاح');
    }

    public function destroy(Transaction $transaction)
    {
        abort_unless(
            auth()->user()->can('delete transactions') &&
                $this->userCanAccessTransaction($transaction),
            403
        );

        $this->transactionService->delete($transaction);

        return redirect()
            ->route('admin.transactions.index')
            ->with('success', 'تم حذف المعاملة بنجاح');
    }

    public function archive(Request $request, Transaction $transaction)
    {
        abort_unless(
            auth()->user()->can('close transactions') &&
                $this->userCanAccessTransaction($transaction),
            403
        );

        $this->transactionService->archive(
            $transaction,
            $request->input('archive_notes')
        );

        return redirect()
            ->route('admin.transactions.index')
            ->with('success', 'تم أرشفة المعاملة بنجاح');
    }

    public function unarchive(Transaction $transaction)
    {
        abort_unless(
            auth()->user()->can('restore archive') ||
                auth()->user()->can('close transactions'),
            403
        );

        $this->transactionService->unarchive($transaction);

        return redirect()
            ->route('admin.transactions.show', $transaction)
            ->with('success', 'تم إلغاء أرشفة المعاملة بنجاح');
    }
    public function archived(Request $request)
    {
        abort_unless(
            auth()->user()->can('view archive') ||
                auth()->user()->can('view transactions'),
            403
        );

        $transactions = $this->transactionsQuery($request)
            ->archived()
            ->with(['archivedBy', 'documents'])
            ->latest('archived_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.transactions.archived', compact('transactions'));
    }

    public function export(Request $request)
    {
        abort_unless(auth()->user()->can('view transactions'), 403);

        $transactions = $this->transactionsQuery($request)
            ->active()
            ->latest()
            ->get();

        return $this->downloadTransactionsExcel(
            $transactions,
            'المعاملات',
            'transactions-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportArchived(Request $request)
    {
        abort_unless(
            auth()->user()->can('view archive') ||
                auth()->user()->can('view transactions'),
            403
        );

        $transactions = $this->transactionsQuery($request)
            ->archived()
            ->with(['archivedBy'])
            ->latest('archived_at')
            ->get();

        return $this->downloadArchivedTransactionsExcel(
            $transactions,
            'أرشيف المعاملات',
            'transactions-archive-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    private function transactionsQuery(Request $request)
    {
        $user = auth()->user();

        return Transaction::query()
            ->with([
                'client',
                'transactionType',
                'assignedUser',
                'technicalManager',
                'coordinator',
                'financialUser',
            ])
            ->when(
                !$user->can('view transactions') &&
                    $user->can('view assigned transactions'),
                function ($query) use ($user) {
                    $query->where(function ($q) use ($user) {
                        $q->where('assigned_to', $user->id)
                            ->orWhere('technical_manager_id', $user->id)
                            ->orWhere('coordinator_id', $user->id)
                            ->orWhere('financial_user_id', $user->id);
                    });
                }
            )
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('reference_number', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('project_name', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('region', 'like', "%{$search}%")
                        ->orWhere('center_request_number', 'like', "%{$search}%")
                        ->orWhere('permit_number', 'like', "%{$search}%")
                        ->orWhereHas('client', function ($clientQuery) use ($search) {
                            $clientQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('facility_name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('client_id'), function ($query) use ($request) {
                $query->where('client_id', $request->client_id);
            })
            ->when($request->filled('transaction_type_id'), function ($query) use ($request) {
                $query->where('transaction_type_id', $request->transaction_type_id);
            })
            ->when($request->filled('assigned_to'), function ($query) use ($request) {
                $userId = $request->assigned_to;

                $query->where(function ($q) use ($userId) {
                    $q->where('assigned_to', $userId)
                        ->orWhere('technical_manager_id', $userId)
                        ->orWhere('coordinator_id', $userId)
                        ->orWhere('financial_user_id', $userId);
                });
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->date_to);
            });
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

    private function downloadTransactionsExcel($transactions, string $title, string $filename)
    {
        $headings = [
            'رقم المعاملة',
            'العميل',
            'نوع المعاملة',
            'عنوان المعاملة',
            'حالة المعاملة',
            'اسم المشروع',
            'المدينة',
            'المنطقة',
            'نوع النشاط',
            'كود النشاط',
            'التصنيف / الفئة',
            'رقم الطلب في المركز',
            'اسم الجهة',
            'رقم مرجع الجهة',
            'رقم التصريح',
            'تاريخ إصدار التصريح',
            'تاريخ انتهاء التصريح',
            'المسؤول الرئيسي',
            'المدير الفني',
            'المنسق',
            'المسؤول المالي',
            'تاريخ البداية',
            'تاريخ التسليم المتوقع',
            'رابط Drive الرئيسي',
            'رابط اجتماعات Drive',
            'ملاحظات',
            'تاريخ إنشاء المعاملة',
        ];

        $rows = $transactions->map(function ($transaction) {
            return [
                (string) $transaction->reference_number,
                $transaction->client?->name,
                $transaction->transactionType?->name,
                $transaction->title,
                $transaction->status,
                $transaction->project_name,
                $transaction->city,
                $transaction->region,
                $transaction->activity_type,
                (string) $transaction->activity_code,
                $transaction->category,
                (string) $transaction->center_request_number,
                $transaction->authority_name,
                (string) $transaction->authority_reference_number,
                (string) $transaction->permit_number,
                $this->formatDate($transaction->permit_issued_at),
                $this->formatDate($transaction->permit_expires_at),
                $transaction->assignedUser?->name,
                $transaction->technicalManager?->name,
                $transaction->coordinator?->name,
                $transaction->financialUser?->name,
                $this->formatDate($transaction->started_at),
                $this->formatDate($transaction->expected_delivery_at),
                $transaction->main_drive_link,
                $transaction->meetings_drive_link,
                $transaction->notes,
                $transaction->created_at?->format('Y-m-d'),
            ];
        })->toArray();

        return Excel::download(
            new ArrayExport(
                $headings,
                $rows,
                $title,
                $this->textColumns()
            ),
            $filename
        );
    }

    private function downloadArchivedTransactionsExcel($transactions, string $title, string $filename)
    {
        $headings = [
            'رقم المعاملة',
            'العميل',
            'نوع المعاملة',
            'عنوان المعاملة',
            'حالة المعاملة',
            'اسم المشروع',
            'المدينة',
            'المنطقة',
            'نوع النشاط',
            'كود النشاط',
            'التصنيف / الفئة',
            'رقم الطلب في المركز',
            'اسم الجهة',
            'رقم مرجع الجهة',
            'رقم التصريح',
            'تاريخ إصدار التصريح',
            'تاريخ انتهاء التصريح',
            'المسؤول الرئيسي',
            'المدير الفني',
            'المنسق',
            'المسؤول المالي',
            'تاريخ البداية',
            'تاريخ التسليم المتوقع',
            'تمت الأرشفة بواسطة',
            'تاريخ الأرشفة',
            'ملاحظات الأرشفة',
            'تاريخ إنشاء المعاملة',
        ];

        $rows = $transactions->map(function ($transaction) {
            return [
                (string) $transaction->reference_number,
                $transaction->client?->name,
                $transaction->transactionType?->name,
                $transaction->title,
                $transaction->status,
                $transaction->project_name,
                $transaction->city,
                $transaction->region,
                $transaction->activity_type,
                (string) $transaction->activity_code,
                $transaction->category,
                (string) $transaction->center_request_number,
                $transaction->authority_name,
                (string) $transaction->authority_reference_number,
                (string) $transaction->permit_number,
                $this->formatDate($transaction->permit_issued_at),
                $this->formatDate($transaction->permit_expires_at),
                $transaction->assignedUser?->name,
                $transaction->technicalManager?->name,
                $transaction->coordinator?->name,
                $transaction->financialUser?->name,
                $this->formatDate($transaction->started_at),
                $this->formatDate($transaction->expected_delivery_at),
                $transaction->archivedBy?->name,
                $transaction->archived_at?->format('Y-m-d'),
                $transaction->archive_notes,
                $transaction->created_at?->format('Y-m-d'),
            ];
        })->toArray();

        return Excel::download(
            new ArrayExport(
                $headings,
                $rows,
                $title,
                $this->textColumns()
            ),
            $filename
        );
    }

    private function formatDate($date): ?string
    {
        return $date ? Carbon::parse($date)->format('Y-m-d') : null;
    }

    private function textColumns(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT,
            'N' => NumberFormat::FORMAT_TEXT,
            'O' => NumberFormat::FORMAT_TEXT,
        ];
    }
}