<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ArrayExport;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ReportController extends Controller
{
    public function financial(Request $request)
    {
        abort_unless(auth()->user()->can('view reports'), 403);

        $transactions = $this->financialTransactionsQuery($request)->get();

        $financialRows = $this->buildFinancialRows($transactions);
        $summary = $this->buildFinancialSummary($financialRows, $transactions);

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

        return view('admin.reports.financial', compact(
            'financialRows',
            'summary',
            'clients',
            'transactionTypes',
            'users'
        ));
    }

    public function financialExport(Request $request)
    {
        abort_unless(auth()->user()->can('view reports'), 403);

        $transactions = $this->financialTransactionsQuery($request)->get();

        $financialRows = $this->buildFinancialRows($transactions);

        $headings = [
            'رقم المعاملة',
            'العميل',
            'نوع المعاملة',
            'اسم المشروع',
            'المسؤول',
            'حالة المعاملة',
            'قيمة العقد',
            'إجمالي المدفوع',
            'المتبقي من العقد',
            'إجمالي المصروفات',
            'إجمالي العمولات',
            'صافي الربح',
            'تاريخ إنشاء المعاملة',
        ];

        $rows = $financialRows->map(function ($row) {
            $transaction = $row['transaction'];

            return [
                (string) $transaction->reference_number,
                $transaction->client?->name,
                $transaction->transactionType?->name,
                $transaction->project_name,
                $transaction->assignedUser?->name,
                $transaction->status,
                $row['contract_value'],
                $row['paid_total'],
                $row['remaining_amount'],
                $row['expenses_total'],
                $row['commissions_total'],
                $row['net_profit'],
                $transaction->created_at?->format('Y-m-d'),
            ];
        })->toArray();

        return Excel::download(
            new ArrayExport(
                $headings,
                $rows,
                'التقرير المالي',
                [
                    'A' => NumberFormat::FORMAT_TEXT,
                    'G' => NumberFormat::FORMAT_NUMBER_00,
                    'H' => NumberFormat::FORMAT_NUMBER_00,
                    'I' => NumberFormat::FORMAT_NUMBER_00,
                    'J' => NumberFormat::FORMAT_NUMBER_00,
                    'K' => NumberFormat::FORMAT_NUMBER_00,
                    'L' => NumberFormat::FORMAT_NUMBER_00,
                ]
            ),
            'financial-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    private function financialTransactionsQuery(Request $request)
    {
        return Transaction::query()
            ->with([
                'client',
                'transactionType',
                'assignedUser',
                'contract',
                'payments',
                'expenses',
                'commissions',
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
            ->latest();
    }

    private function buildFinancialRows($transactions)
    {
        return $transactions->map(function ($transaction) {
            $contractValue = (float) ($transaction->contract?->contract_value ?? 0);

            $paidTotal = (float) $transaction->payments
                ->where('status', 'مدفوعة')
                ->sum('amount');

            $expensesTotal = (float) $transaction->expenses
                ->where('status', '!=', 'ملغي')
                ->sum('amount');

            $commissionsTotal = (float) $transaction->commissions
                ->where('status', '!=', 'ملغية')
                ->sum('calculated_amount');

            $remainingAmount = max($contractValue - $paidTotal, 0);

            $netProfit = $paidTotal - $expensesTotal - $commissionsTotal;

            return [
                'transaction' => $transaction,
                'contract_value' => $contractValue,
                'paid_total' => $paidTotal,
                'remaining_amount' => $remainingAmount,
                'expenses_total' => $expensesTotal,
                'commissions_total' => $commissionsTotal,
                'net_profit' => $netProfit,
            ];
        });
    }

    private function buildFinancialSummary($financialRows, $transactions): array
    {
        return [
            'transactions_count' => $transactions->count(),
            'contracts_total' => $financialRows->sum('contract_value'),
            'paid_total' => $financialRows->sum('paid_total'),
            'remaining_total' => $financialRows->sum('remaining_amount'),
            'expenses_total' => $financialRows->sum('expenses_total'),
            'commissions_total' => $financialRows->sum('commissions_total'),
            'net_profit_total' => $financialRows->sum('net_profit'),
        ];
    }
}