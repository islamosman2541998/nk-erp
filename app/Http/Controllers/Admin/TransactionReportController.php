<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ArrayExport;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TransactionReportController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('view reports'), 403);

        $transactions = $this->transactionsQuery($request)->get();

        $summary = $this->buildSummary($transactions);

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

        return view('admin.reports.transactions', compact(
            'transactions',
            'summary',
            'clients',
            'transactionTypes',
            'users'
        ));
    }

    public function export(Request $request)
    {
        abort_unless(auth()->user()->can('view reports'), 403);

        $transactions = $this->transactionsQuery($request)->get();

        $headings = [
            'رقم المعاملة',
            'العميل',
            'نوع المعاملة',
            'اسم المشروع',
            'الحالة',
            'المسؤول الرئيسي',
            'المدير الفني',
            'المنسق',
            'المدينة',
            'المنطقة',
            'تاريخ البداية',
            'تاريخ التسليم المتوقع',
            'تاريخ الإنشاء',
        ];

        $rows = $transactions->map(function ($transaction) {
            return [
                (string) $transaction->reference_number,
                $transaction->client?->name,
                $transaction->transactionType?->name,
                $transaction->project_name,
                $transaction->status,
                $transaction->assignedUser?->name,
                $transaction->technicalManager?->name,
                $transaction->coordinator?->name,
                $transaction->city,
                $transaction->region,
                $transaction->started_at ? Carbon::parse($transaction->started_at)->format('Y-m-d') : null,
                $transaction->expected_delivery_at ? Carbon::parse($transaction->expected_delivery_at)->format('Y-m-d') : null,
                $transaction->created_at?->format('Y-m-d'),
            ];
        })->toArray();

        return Excel::download(
            new ArrayExport(
                $headings,
                $rows,
                'تقرير المعاملات',
                [
                    'A' => NumberFormat::FORMAT_TEXT,
                ]
            ),
            'transactions-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    private function transactionsQuery(Request $request)
    {
        return Transaction::query()
            ->with([
                'client',
                'transactionType',
                'assignedUser',
                'technicalManager',
                'coordinator',
            ])
            ->active()
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->date_to);
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
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest();
    }

    private function buildSummary($transactions): array
    {
        $closedStatuses = ['مكتملة', 'مغلقة'];

        $lateTransactions = $transactions->filter(function ($transaction) use ($closedStatuses) {
            if (!$transaction->expected_delivery_at) {
                return false;
            }

            return Carbon::parse($transaction->expected_delivery_at)->lt(today())
                && !in_array($transaction->status, $closedStatuses);
        });

        $nearDeliveryTransactions = $transactions->filter(function ($transaction) use ($closedStatuses) {
            if (!$transaction->expected_delivery_at) {
                return false;
            }

            $expectedDate = Carbon::parse($transaction->expected_delivery_at);

            return $expectedDate->between(today(), today()->copy()->addDays(7))
                && !in_array($transaction->status, $closedStatuses);
        });

        return [
            'total' => $transactions->count(),
            'late_count' => $lateTransactions->count(),
            'near_delivery_count' => $nearDeliveryTransactions->count(),

            'by_status' => $transactions
                ->groupBy('status')
                ->map(fn($items) => $items->count())
                ->sortDesc(),

            'by_type' => $transactions
                ->groupBy(fn($transaction) => $transaction->transactionType?->name ?? 'غير محدد')
                ->map(fn($items) => $items->count())
                ->sortDesc(),

            'by_assigned_user' => $transactions
                ->groupBy(fn($transaction) => $transaction->assignedUser?->name ?? 'غير محدد')
                ->map(fn($items) => $items->count())
                ->sortDesc(),

            'late_transactions' => $lateTransactions,
            'near_delivery_transactions' => $nearDeliveryTransactions,
        ];
    }
}