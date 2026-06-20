<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ArrayExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientRequest;
use App\Http\Requests\Admin\UpdateClientRequest;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ClientController extends Controller
{
    public function __construct(
        private readonly ClientService $clientService
    ) {}

    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('view clients'), 403);

        $clients = $this->clientsQuery($request)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('create clients'), 403);

        return view('admin.clients.create');
    }

    public function store(StoreClientRequest $request)
    {
        abort_unless(auth()->user()->can('create clients'), 403);

        $client = $this->clientService->create($request->validated());

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('success', 'تم إضافة العميل بنجاح');
    }

    public function show(Client $client)
    {
        abort_unless(auth()->user()->can('view clients'), 403);

        $client->loadCount('transactions');

        $latestTransactions = $client->transactions()
            ->with('transactionType')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.clients.show', compact('client', 'latestTransactions'));
    }

    public function edit(Client $client)
    {
        abort_unless(auth()->user()->can('edit clients'), 403);

        return view('admin.clients.edit', compact('client'));
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        abort_unless(auth()->user()->can('edit clients'), 403);

        $this->clientService->update($client, $request->validated());

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('success', 'تم تعديل بيانات العميل بنجاح');
    }

    public function destroy(Client $client)
    {
        abort_unless(auth()->user()->can('delete clients'), 403);

        $this->clientService->delete($client);

        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'تم حذف العميل بنجاح');
    }

    public function export(Request $request)
    {
        abort_unless(auth()->user()->can('view clients'), 403);

        $clients = $this->clientsQuery($request)
            ->latest()
            ->get();

        $headings = [
            'الاسم',
            'اسم المنشأة',
            'السجل التجاري',
            'الرقم الضريبي',
            'الهاتف',
            'البريد الإلكتروني',
            'اسم مسؤول التواصل',
            'هاتف مسؤول التواصل',
            'المدينة',
            'المنطقة',
            'العنوان',
            'تاريخ الإضافة',
        ];

        $rows = $clients->map(function ($client) {
            return [
                $client->name,
                $client->facility_name,
                (string) $client->commercial_registration_number,
                (string) $client->tax_number,
                (string) $client->phone,
                $client->email,
                $client->contact_person_name,
                (string) $client->contact_person_phone,
                $client->city,
                $client->region,
                $client->address,
                $client->created_at?->format('Y-m-d'),
            ];
        })->toArray();

        return Excel::download(
            new ArrayExport(
                $headings,
                $rows,
                'العملاء',
                [
                    'C' => NumberFormat::FORMAT_TEXT,
                    'D' => NumberFormat::FORMAT_TEXT,
                    'E' => NumberFormat::FORMAT_TEXT,
                    'H' => NumberFormat::FORMAT_TEXT,
                ]
            ),
            'clients-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    private function clientsQuery(Request $request)
    {
        return Client::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('facility_name', 'like', "%{$search}%")
                        ->orWhere('commercial_registration_number', 'like', "%{$search}%")
                        ->orWhere('tax_number', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
    }
}