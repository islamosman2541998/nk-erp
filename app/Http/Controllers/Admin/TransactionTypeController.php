<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransactionTypeController extends Controller
{
    public function index()
    {
        $transactionTypes = TransactionType::query()
            ->with(['documentRequirements' => function ($query) {
                $query->orderBy('sort_order');
            }])
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return view('admin.transaction-types.index', compact('transactionTypes'));
    }

    public function create()
    {
        $parentTypes = TransactionType::query()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return view('admin.transaction-types.create', compact('parentTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_id' => ['nullable', 'exists:transaction_types,id'],
            'name' => ['required', 'string', 'max:255', 'unique:transaction_types,name'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        TransactionType::create([
            'parent_id' => $data['parent_id'] ?? null,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return redirect()
            ->route('admin.transaction-types.index')
            ->with('success', 'تم إضافة نوع المعاملة بنجاح');
    }

    public function edit(TransactionType $transactionType)
    {
        $transactionType->load(['documentRequirements' => function ($query) {
            $query->orderBy('sort_order');
        }]);

        $parentTypes = TransactionType::query()
            ->whereNull('parent_id')
            ->where('id', '!=', $transactionType->id)
            ->orderBy('sort_order')
            ->get();

        return view('admin.transaction-types.edit', compact('transactionType', 'parentTypes'));
    }

    public function update(Request $request, TransactionType $transactionType)
    {
        $data = $request->validate([
            'parent_id' => ['nullable', 'exists:transaction_types,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('transaction_types', 'name')->ignore($transactionType->id),
            ],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $transactionType->update([
            'parent_id' => $data['parent_id'] ?? null,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return redirect()
            ->route('admin.transaction-types.index')
            ->with('success', 'تم تعديل نوع المعاملة بنجاح');
    }

    public function destroy(TransactionType $transactionType)
    {
        if ($transactionType->transactions()->exists() || $transactionType->children()->exists()) {
            $transactionType->update([
                'is_active' => false,
            ]);

            return redirect()
                ->route('admin.transaction-types.index')
                ->with('success', 'لا يمكن حذف النوع لأنه مرتبط بمعاملات، تم تعطيله بدل الحذف');
        }

        $transactionType->documentRequirements()->delete();
        $transactionType->delete();

        return redirect()
            ->route('admin.transaction-types.index')
            ->with('success', 'تم حذف نوع المعاملة بنجاح');
    }
}