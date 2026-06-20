<x-app-layout>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="nk-page-title">الأدوار والصلاحيات</h1>
            <p class="nk-page-subtitle">
                إدارة أدوار المستخدمين وتحديد الصلاحيات الخاصة بكل دور داخل النظام.
            </p>
        </div>

        <a href="{{ route('admin.roles.create') }}" class="nk-btn-main">
            <i class="bi bi-plus-circle"></i>
            إضافة دور
        </a>
    </div>

    <div class="nk-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>اسم الدور</th>
                        <th>عدد الصلاحيات</th>
                        <th>ملاحظات</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td class="fw-bold">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="nk-role-icon">
                                        <i class="bi bi-shield-lock"></i>
                                    </span>

                                    <span>{{ config('roles.labels.' . $role->name, $role->name) }}</span>

                                    @if($role->name === 'CEO')
                                        <span class="badge bg-success-subtle text-success rounded-pill">
                                            مدير النظام
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                    {{ $role->permissions_count }} صلاحية
                                </span>
                            </td>

                            <td>
                                @if($role->name === 'CEO')
                                    <span class="text-muted small">
                                        هذا الدور يحصل على كل الصلاحيات تلقائيًا.
                                    </span>
                                @else
                                    <span class="text-muted small">
                                        يمكن تعديل الصلاحيات الخاصة بهذا الدور.
                                    </span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex align-items-center gap-1 flex-nowrap">
                                    <a href="{{ route('admin.roles.edit', $role) }}"
                                       class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="bi bi-pencil-square"></i>
                                        تعديل
                                    </a>

                                    @if($role->name !== 'CEO')
                                        <form method="POST"
                                              action="{{ route('admin.roles.destroy', $role) }}"
                                              class="js-confirm-form m-0"
                                              data-title="حذف الدور"
                                              data-text="هل أنت متأكد من حذف هذا الدور؟ لا يمكن حذف دور مرتبط بمستخدمين."
                                              data-icon="warning"
                                              data-confirm-text="نعم، احذف"
                                              data-cancel-text="إلغاء"
                                              data-confirm-color="#c0392b">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                                <i class="bi bi-trash"></i>
                                                حذف
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                لا توجد أدوار حتى الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $roles->links() }}
        </div>
    </div>
</x-app-layout>