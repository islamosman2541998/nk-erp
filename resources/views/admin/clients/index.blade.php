<x-app-layout>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="nk-page-title">العملاء</h1>
            <p class="nk-page-subtitle">إدارة بيانات العملاء والمنشآت قبل ربطها بالمعاملات.</p>
        </div>
        <div>
            @can('view clients')
                <a href="{{ route('admin.clients.export', request()->query()) }}"
                    class="btn btn-outline-success rounded-pill">
                    <i class="bi bi-file-earmark-excel"></i>
                    تصدير Excel
                </a>
            @endcan
            @can('create clients')
                <a href="{{ route('admin.clients.create') }}" class="nk-btn-main">
                    <i class="bi bi-plus-circle"></i>
                    إضافة عميل
                </a>
            @endcan

        </div>

    </div>



    <div class="nk-card mb-4">
        <form method="GET" action="{{ route('admin.clients.index') }}" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                    placeholder="بحث باسم العميل، المنشأة، السجل التجاري، الرقم الضريبي، الجوال...">
            </div>
            <div class="col-md-2">
                <button class="btn btn-success w-100 rounded-pill">
                    بحث
                </button>
            </div>
        </form>
    </div>

    <div class="nk-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>اسم العميل</th>
                        <th>المنشأة</th>
                        <th>السجل التجاري</th>
                        <th>الرقم الضريبي</th>
                        <th>الجوال</th>
                        <th>المدينة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr>
                            <td class="fw-bold">{{ $client->name }}</td>
                            <td>{{ $client->facility_name ?? '-' }}</td>
                            <td>{{ $client->commercial_registration_number ?? '-' }}</td>
                            <td>{{ $client->tax_number ?? '-' }}</td>
                            <td>{{ $client->phone ?? '-' }}</td>
                            <td>{{ $client->city ?? '-' }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.clients.show', $client) }}"
                                        class="btn btn-sm btn-outline-success rounded-pill">
                                        <i class="bi bi-eye"></i>
                                        عرض
                                    </a>

                                    @can('edit clients')
                                        <a href="{{ route('admin.clients.edit', $client) }}"
                                            class="btn btn-sm btn-outline-primary rounded-pill">
                                            <i class="bi bi-pencil-square"></i>
                                            تعديل
                                        </a>
                                    @endcan

                                    @can('delete clients')
                                        <form method="POST" action="{{ route('admin.clients.destroy', $client) }}"
                                            class="js-confirm-form" data-title="حذف العميل"
                                            data-text="هل أنت متأكد من حذف هذا العميل؟" data-icon="warning"
                                            data-confirm-text="نعم، احذف" data-cancel-text="إلغاء"
                                            data-confirm-color="#c0392b">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                                <i class="bi bi-trash"></i>
                                                حذف
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                لا توجد بيانات عملاء حتى الآن
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $clients->links() }}
        </div>
    </div>
</x-app-layout>
