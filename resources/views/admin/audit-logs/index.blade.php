<x-app-layout>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="nk-page-title">سجل العمليات</h1>
            <p class="nk-page-subtitle">
                متابعة كل العمليات التي تمت داخل النظام.
            </p>
        </div>
    </div>

    <div class="nk-card mb-4">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">بحث</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                    placeholder="بحث في الوصف أو النوع...">
            </div>

            <div class="col-md-2">
                <label class="form-label">المستخدم</label>
                <select name="user_id" class="form-select">
                    <option value="">كل المستخدمين</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">العملية</label>
                <select name="event" class="form-select">
                    <option value="">كل العمليات</option>
                    <option value="created" @selected(request('event') === 'created')>إنشاء</option>
                    <option value="updated" @selected(request('event') === 'updated')>تعديل</option>
                    <option value="deleted" @selected(request('event') === 'deleted')>حذف</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">القسم</label>
                <select name="log_name" class="form-select">
                    <option value="">كل الأقسام</option>
                    @foreach ($logNames as $logName)
                        <option value="{{ $logName }}" @selected(request('log_name') === $logName)>
                            {{ config('audit.logs.' . $logName, $logName) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-1">
                <label class="form-label">من</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <div class="col-md-1">
                <label class="form-label">إلى</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-success w-100 rounded-pill">
                    فلترة
                </button>
            </div>

            <div class="col-12 d-flex justify-content-end">
                <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary rounded-pill">
                    إعادة ضبط
                </a>
            </div>
        </form>
    </div>

    <div class="nk-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>المستخدم</th>
                        <th>القسم</th>
                        <th>العملية</th>
                        <th>الوصف</th>
                        <th>العنصر</th>
                        <th>التغييرات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($activities as $activity)
                        <tr>
                            <td>
                                <div class="fw-bold">
                                    {{ $activity->created_at?->format('Y-m-d') }}
                                </div>
                                <div class="text-muted small">
                                    {{ $activity->created_at?->format('H:i') }}
                                </div>
                            </td>

                            <td>
                                {{ $activity->causer?->name ?? 'النظام' }}
                            </td>

                            <td>
                                <span class="badge bg-primary-subtle text-primary rounded-pill">
                                    {{ config('audit.logs.' . $activity->log_name, $activity->log_name) }}
                                </span>
                            </td>

                            <td>
                                @php
                                    $eventLabel = match ($activity->event) {
                                        'created' => 'إنشاء',
                                        'updated' => 'تعديل',
                                        'deleted' => 'حذف',
                                        default => $activity->event ?? '-',
                                    };

                                    $eventClass = match ($activity->event) {
                                        'created' => 'bg-success-subtle text-success',
                                        'updated' => 'bg-warning-subtle text-warning',
                                        'deleted' => 'bg-danger-subtle text-danger',
                                        default => 'bg-secondary-subtle text-secondary',
                                    };
                                @endphp

                                <span class="badge {{ $eventClass }} rounded-pill">
                                    {{ $eventLabel }}
                                </span>
                            </td>

                            <td>
                                {{ $activity->description }}
                            </td>

                            <td>
                                <div class="text-muted small">
                                    {{ class_basename($activity->subject_type) }}
                                    #{{ $activity->subject_id }}
                                </div>
                            </td>

                            <td>
                                @php
                                    $properties = $activity->properties?->toArray() ?? [];
                                    $oldValues = $properties['old'] ?? [];
                                    $newValues = $properties['attributes'] ?? [];
                                    $fieldLabels = config('audit.fields', []);
                                    $changedKeys = collect(
                                        array_unique(array_merge(array_keys($oldValues), array_keys($newValues))),
                                    );
                                @endphp

                                @if ($changedKeys->count())
                                    <details>
                                        <summary class="text-success fw-bold" style="cursor: pointer;">
                                            عرض
                                        </summary>

                                        <div class="table-responsive mt-2">
                                            <table class="table table-sm table-bordered align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>الحقل</th>
                                                        <th>قبل</th>
                                                        <th>بعد</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($changedKeys as $key)
                                                        <tr>
                                                            <td class="fw-bold">
                                                                {{ $fieldLabels[$key] ?? $key }}
                                                            </td>

                                                            <td class="text-muted">
                                                                @if (is_array($oldValues[$key] ?? null))
                                                                    {{ json_encode($oldValues[$key], JSON_UNESCAPED_UNICODE) }}
                                                                @else
                                                                    {{ $oldValues[$key] ?? '-' }}
                                                                @endif
                                                            </td>

                                                            <td>
                                                                @if (is_array($newValues[$key] ?? null))
                                                                    {{ json_encode($newValues[$key], JSON_UNESCAPED_UNICODE) }}
                                                                @else
                                                                    {{ $newValues[$key] ?? '-' }}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </details>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                لا توجد عمليات مسجلة حتى الآن
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $activities->links() }}
        </div>
    </div>
</x-app-layout>
