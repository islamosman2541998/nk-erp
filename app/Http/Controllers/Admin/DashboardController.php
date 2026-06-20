<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Transaction;
use App\Models\TransactionDocument;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        abort_unless(
            $user->can('view dashboard') ||
                $user->can('view transactions') ||
                $user->can('view assigned transactions'),
            403
        );

        $transactionsQuery = $this->visibleTransactionsQuery();

        $transactionsCount = (clone $transactionsQuery)
            ->active()
            ->count();

        $inProgressTransactionsCount = (clone $transactionsQuery)
            ->active()
            ->whereNotIn('status', [
                'مكتملة',
                'مغلقة',
                'ملغاة',
                'تم صدور التصريح',
            ])
            ->count();

        $completedTransactionsCount = (clone $transactionsQuery)
            ->active()
            ->whereIn('status', [
                'مكتملة',
                'مغلقة',
                'تم صدور التصريح',
            ])
            ->count();

        $clientsCount = Client::query()->count();

        $missingDocumentsCount = TransactionDocument::query()
            ->whereIn('status', [
                'ناقص',
                'مطلوب',
                'مرفوض',
            ])
            ->whereHas('transaction', function ($query) use ($user) {
                $query->active();

                if (
                    !$user->can('view transactions') &&
                    $user->can('view assigned transactions')
                ) {
                    $query->where(function ($q) use ($user) {
                        $q->where('assigned_to', $user->id)
                            ->orWhere('technical_manager_id', $user->id)
                            ->orWhere('coordinator_id', $user->id)
                            ->orWhere('financial_user_id', $user->id);
                    });
                }
            })
            ->count();

        $latestTransactions = (clone $transactionsQuery)
            ->active()
            ->with([
                'client',
                'transactionType',
                'assignedUser',
            ])
            ->latest()
            ->take(8)
            ->get();

        $expiringPermits = (clone $transactionsQuery)
            ->active()
            ->with([
                'client',
                'transactionType',
            ])
            ->whereNotNull('permit_expires_at')
            ->whereDate('permit_expires_at', '>=', now())
            ->whereDate('permit_expires_at', '<=', now()->addDays(60))
            ->orderBy('permit_expires_at')
            ->take(5)
            ->get();

        $lateTransactions = (clone $transactionsQuery)
            ->active()
            ->with([
                'client',
                'transactionType',
                'assignedUser',
            ])
            ->whereNotNull('expected_delivery_at')
            ->whereDate('expected_delivery_at', '<', now())
            ->whereNotIn('status', [
                'مكتملة',
                'مغلقة',
                'ملغاة',
                'تم صدور التصريح',
            ])
            ->orderBy('expected_delivery_at')
            ->take(5)
            ->get();
        $statusChartData = (clone $transactionsQuery)
            ->active()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->get()
            ->map(fn($row) => [
                'label' => $row->status ?: 'غير محدد',
                'value' => (int) $row->total,
            ])
            ->values();

        $startMonth = now()->copy()->subMonths(5)->startOfMonth();

        $monthlyRaw = (clone $transactionsQuery)
            ->active()
            ->where('created_at', '>=', $startMonth)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, COUNT(*) as total")
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        $monthlyTransactionsChartData = collect(range(5, 0))
            ->map(function ($i) use ($monthlyRaw) {
                $date = now()->copy()->subMonths($i);
                $key = $date->format('Y-m');

                return [
                    'label' => $date->translatedFormat('M Y'),
                    'value' => (int) ($monthlyRaw[$key] ?? 0),
                ];
            })
            ->values();

        $transactionTypeChartData = (clone $transactionsQuery)
            ->active()
            ->leftJoin('transaction_types', 'transactions.transaction_type_id', '=', 'transaction_types.id')
            ->selectRaw("COALESCE(transaction_types.name, 'غير محدد') as type_name, COUNT(transactions.id) as total")
            ->groupBy('type_name')
            ->orderByDesc('total')
            ->limit(8)
            ->get()
            ->map(fn($row) => [
                'label' => $row->type_name,
                'value' => (int) $row->total,
            ])
            ->values();

        $documentsChartData = TransactionDocument::query()
            ->whereHas('transaction', function ($query) use ($user) {
                $query->active();

                if (
                    !$user->can('view transactions') &&
                    $user->can('view assigned transactions')
                ) {
                    $query->where(function ($q) use ($user) {
                        $q->where('assigned_to', $user->id)
                            ->orWhere('technical_manager_id', $user->id)
                            ->orWhere('coordinator_id', $user->id)
                            ->orWhere('financial_user_id', $user->id);
                    });
                }
            })
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->get()
            ->map(fn($row) => [
                'label' => $row->status ?: 'غير محدد',
                'value' => (int) $row->total,
            ])
            ->values();
        return view('dashboard', compact(
            'transactionsCount',
            'inProgressTransactionsCount',
            'completedTransactionsCount',
            'clientsCount',
            'missingDocumentsCount',
            'latestTransactions',
            'expiringPermits',
            'lateTransactions',
            'statusChartData',
            'monthlyTransactionsChartData',
            'transactionTypeChartData',
            'documentsChartData',
        ));
    }

    private function visibleTransactionsQuery(): Builder
    {
        $user = auth()->user();

        $query = Transaction::query();

        if (
            !$user->can('view transactions') &&
            $user->can('view assigned transactions')
        ) {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                    ->orWhere('technical_manager_id', $user->id)
                    ->orWhere('coordinator_id', $user->id)
                    ->orWhere('financial_user_id', $user->id);
            });
        }

        return $query;
    }
}