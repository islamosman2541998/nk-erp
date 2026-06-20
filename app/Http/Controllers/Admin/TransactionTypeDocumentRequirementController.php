<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransactionType;
use App\Models\TransactionTypeDocumentRequirement;
use Illuminate\Http\Request;

class TransactionTypeDocumentRequirementController extends Controller
{
    public function store(Request $request, TransactionType $transactionType)
    {
        abort_unless(auth()->user()->can('edit transaction types'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_required' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $transactionType->documentRequirements()->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_required' => $request->boolean('is_required'),
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return back()->with('success', 'تم إضافة المستند بنجاح');
    }

    public function update(Request $request, TransactionTypeDocumentRequirement $documentRequirement)
    {
        abort_unless(auth()->user()->can('edit transaction types'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_required' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $documentRequirement->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_required' => $request->boolean('is_required'),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return back()->with('success', 'تم تعديل المستند بنجاح');
    }

    public function destroy(TransactionTypeDocumentRequirement $documentRequirement)
    {
        abort_unless(auth()->user()->can('delete transaction types'), 403);

        $documentRequirement->delete();

        return back()->with('success', 'تم حذف المستند بنجاح');
    }
}