<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ArrayExport;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('view contracts'), 403);

        $contracts = $this->contractsQuery($request)
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

        $users = User::query()
            ->orderBy('name')
            ->get();

        return view('admin.contracts.index', compact(
            'contracts',
            'clients',
            'transactionTypes',
            'users'
        ));
    }

    public function export(Request $request)
    {
        abort_unless(auth()->user()->can('view contracts'), 403);

        $contracts = $this->contractsQuery($request)
            ->latest()
            ->get();

        $headings = [
            'رقم العقد',
            'رقم المعاملة',
            'العميل',
            'نوع المعاملة',
            'اسم المشروع',
            'قيمة العقد',
            'العملة',
            'تاريخ العقد',
            'حالة العقد',
            'المسؤول الرئيسي',
            'المدير الفني',
            'المنسق',
            'المسؤول المالي',
            'رابط الملف',
            'رابط Drive',
            'ملاحظات',
            'تاريخ الإنشاء',
        ];

        $rows = $contracts->map(function ($contract) {
            $transaction = $contract->transaction;

            return [
                (string) $contract->contract_number,
                (string) $transaction?->reference_number,
                $transaction?->client?->name,
                $transaction?->transactionType?->name,
                $transaction?->project_name,
                $contract->contract_value,
                $contract->currency,
                $contract->contract_date?->format('Y-m-d'),
                $contract->status,
                $transaction?->assignedUser?->name,
                $transaction?->technicalManager?->name,
                $transaction?->coordinator?->name,
                $transaction?->financialUser?->name,
                $contract->file_path ? asset('storage/' . $contract->file_path) : null,
                $contract->drive_link,
                $contract->notes,
                $contract->created_at?->format('Y-m-d'),
            ];
        })->toArray();

        return Excel::download(
            new ArrayExport(
                $headings,
                $rows,
                'العقود',
                [
                    'A' => NumberFormat::FORMAT_TEXT,
                    'B' => NumberFormat::FORMAT_TEXT,
                    'F' => NumberFormat::FORMAT_NUMBER_00,
                    'N' => NumberFormat::FORMAT_TEXT,
                    'O' => NumberFormat::FORMAT_TEXT,
                ]
            ),
            'contracts-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function store(Request $request, Transaction $transaction)
    {
        abort_unless(
            auth()->user()->can('create contracts') &&
            $this->userCanAccessTransaction($transaction),
            403
        );

        if ($transaction->contract()->exists()) {
            return back()->with('error', 'هذه المعاملة لديها عقد بالفعل');
        }

        $data = $request->validate([
            'contract_number' => ['nullable', 'string', 'max:255'],
            'contract_date' => ['nullable', 'date'],
            'contract_value' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'status' => ['required', 'string', 'max:255'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
            'drive_link' => ['nullable', 'url'],
            'notes' => ['nullable', 'string'],
        ]);

        $contractData = [
            'transaction_id' => $transaction->id,
            'contract_number' => $data['contract_number'] ?? null,
            'contract_date' => $data['contract_date'] ?? null,
            'contract_value' => $data['contract_value'] ?? null,
            'currency' => $data['currency'] ?? app(\App\Services\SettingService::class)->get('default_currency', 'SAR'),
            'status' => $data['status'],
            'drive_link' => $data['drive_link'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => auth()->id(),
        ];

        if ($request->hasFile('file')) {
            $contractData['file_path'] = $request->file('file')->store(
                'transactions/' . $transaction->id . '/contracts',
                'public'
            );
        }

        $transaction->contract()->create($contractData);

        return back()->with('success', 'تم إضافة العقد بنجاح');
    }

    public function update(Request $request, Contract $contract)
    {
        $contract->load('transaction');

        abort_unless(
            auth()->user()->can('edit contracts') &&
            $this->userCanAccessTransaction($contract->transaction),
            403
        );

        $data = $request->validate([
            'contract_number' => ['nullable', 'string', 'max:255'],
            'contract_date' => ['nullable', 'date'],
            'contract_value' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'status' => ['required', 'string', 'max:255'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
            'drive_link' => ['nullable', 'url'],
            'notes' => ['nullable', 'string'],
            'clear_file' => ['nullable', 'boolean'],
        ]);

        $contractData = [
            'contract_number' => $data['contract_number'] ?? null,
            'contract_date' => $data['contract_date'] ?? null,
            'contract_value' => $data['contract_value'] ?? null,
            'currency' => $data['currency'] ?? app(\App\Services\SettingService::class)->get('default_currency', 'SAR'),
            'status' => $data['status'],
            'drive_link' => $data['drive_link'] ?? null,
            'notes' => $data['notes'] ?? null,
            'updated_by' => auth()->id(),
        ];

        if ($request->boolean('clear_file')) {
            if ($contract->file_path) {
                Storage::disk('public')->delete($contract->file_path);
            }

            $contractData['file_path'] = null;
        }

        if ($request->hasFile('file')) {
            if ($contract->file_path) {
                Storage::disk('public')->delete($contract->file_path);
            }

            $contractData['file_path'] = $request->file('file')->store(
                'transactions/' . $contract->transaction_id . '/contracts',
                'public'
            );
        }

        $contract->update($contractData);

        return back()->with('success', 'تم تعديل العقد بنجاح');
    }

    public function destroy(Contract $contract)
    {
        $contract->load('transaction');

        abort_unless(
            auth()->user()->can('delete contracts') &&
            $this->userCanAccessTransaction($contract->transaction),
            403
        );

        if ($contract->file_path) {
            Storage::disk('public')->delete($contract->file_path);
        }

        $contract->delete();

        return back()->with('success', 'تم حذف العقد بنجاح');
    }

    private function contractsQuery(Request $request)
    {
        $user = auth()->user();

        return Contract::query()
            ->with([
                'transaction.client',
                'transaction.transactionType',
                'transaction.assignedUser',
                'transaction.technicalManager',
                'transaction.coordinator',
                'transaction.financialUser',
            ])
            ->whereHas('transaction')
            ->when(
                !$user->can('view transactions') && $user->can('view assigned transactions'),
                function ($query) use ($user) {
                    $query->whereHas('transaction', function ($transactionQuery) use ($user) {
                        $transactionQuery->where(function ($q) use ($user) {
                            $q->where('assigned_to', $user->id)
                                ->orWhere('technical_manager_id', $user->id)
                                ->orWhere('coordinator_id', $user->id)
                                ->orWhere('financial_user_id', $user->id);
                        });
                    });
                }
            )
            ->when(
                !$user->can('view transactions') && !$user->can('view assigned transactions'),
                function ($query) {
                    $query->whereRaw('1 = 0');
                }
            )
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('contract_number', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('transaction', function ($transactionQuery) use ($search) {
                            $transactionQuery->where('reference_number', 'like', "%{$search}%")
                                ->orWhere('project_name', 'like', "%{$search}%")
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
            ->when($request->filled('assigned_to'), function ($query) use ($request) {
                $userId = $request->assigned_to;

                $query->whereHas('transaction', function ($transactionQuery) use ($userId) {
                    $transactionQuery->where(function ($q) use ($userId) {
                        $q->where('assigned_to', $userId)
                            ->orWhere('technical_manager_id', $userId)
                            ->orWhere('coordinator_id', $userId)
                            ->orWhere('financial_user_id', $userId);
                    });
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('contract_date', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('contract_date', '<=', $request->date_to);
            })
            ->when($request->filled('archive_status'), function ($query) use ($request) {
                if ($request->archive_status === 'active') {
                    $query->whereHas('transaction', function ($transactionQuery) {
                        $transactionQuery->active();
                    });
                }

                if ($request->archive_status === 'archived') {
                    $query->whereHas('transaction', function ($transactionQuery) {
                        $transactionQuery->archived();
                    });
                }
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
}