<x-app-layout>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="nk-page-title">المستخدمين</h1>
            <p class="nk-page-subtitle">
                إدارة حسابات المستخدمين وتحديد أدوارهم وصلاحياتهم داخل النظام.
            </p>
        </div>

        <a href="{{ route('admin.users.create') }}" class="nk-btn-main">
            <i class="bi bi-person-plus"></i>
            إضافة مستخدم
        </a>
    </div>

    <div class="nk-card mb-4">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-10">
                    <label class="form-label">بحث</label>
                    <input type="text"
                           name="search"
                           class="form-control"
                           value="{{ request('search') }}"
                           placeholder="بحث بالاسم أو البريد الإلكتروني">
                </div>

                <div class="col-md-2">
                    <button class="nk-btn-main w-100">
                        بحث
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="nk-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الأدوار</th>
                        <th>صلاحيات مباشرة</th>
                        <th>تاريخ الإنشاء</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="fw-bold">
                                {{ $user->name }}
                            </td>

                            <td dir="ltr">
                                {{ $user->email }}
                            </td>

                            <td>
                                @forelse($user->roles as $role)
                                    <span class="badge bg-success-subtle text-success rounded-pill">
                                        {{ config('roles.labels.' . $role->name, $role->name) }}
                                    </span>
                                @empty
                                    <span class="text-muted">لا يوجد</span>
                                @endforelse
                            </td>

                            <td>
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                    {{ $user->permissions->count() }} صلاحية
                                </span>
                            </td>

                            <td>
                                {{ $user->created_at?->format('Y-m-d') }}
                            </td>

                            <td>
                                <div class="d-flex align-items-center gap-1 flex-nowrap">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="bi bi-pencil-square"></i>
                                        تعديل
                                    </a>

                                    @if($user->id !== auth()->id() && !$user->hasRole('CEO'))
                                        <form method="POST"
                                              action="{{ route('admin.users.destroy', $user) }}"
                                              class="js-confirm-form m-0"
                                              data-title="حذف المستخدم"
                                              data-text="هل أنت متأكد من حذف هذا المستخدم؟"
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
                            <td colspan="6" class="text-center text-muted py-4">
                                لا توجد مستخدمين حتى الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>