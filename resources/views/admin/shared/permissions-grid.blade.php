@php
    $selectedPermissions = collect($selectedPermissions ?? []);

    $permissionGroups = [
        'لوحة التحكم' => [
            'view dashboard',
        ],

        'المستخدمين والأدوار' => [
            'manage users',
            'manage roles',
        ],

        'العملاء' => [
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',
        ],

        'أنواع المعاملات' => [
            'view transaction types',
            'create transaction types',
            'edit transaction types',
            'delete transaction types',
        ],

        'المعاملات' => [
            'view transactions',
            'view assigned transactions',
            'create transactions',
            'edit transactions',
            'delete transactions',
            'close transactions',
            'change transaction status',
        ],

        'المستندات والمرفقات' => [
            'view attachments',
            'upload attachments',
            'review attachments',
            'delete attachments',
        ],

        'الأرشيف' => [
            'view archive',
            'restore archive',
        ],

        'العقود' => [
            'view contracts',
            'create contracts',
            'edit contracts',
            'delete contracts',
        ],

        'الدفعات' => [
            'view payments',
            'create payments',
            'edit payments',
            'delete payments',
        ],

        'المصروفات' => [
            'view expenses',
            'create expenses',
            'edit expenses',
            'delete expenses',
        ],

        'العمولات' => [
            'view commissions',
            'create commissions',
            'edit commissions',
            'delete commissions',
        ],

        'التقارير والسجلات' => [
            'view reports',
            'view audit logs',
        ],

        'الإعدادات' => [
            'edit settings',
        ],
    ];

    $permissionLabels = [
        'view dashboard' => 'عرض لوحة التحكم',

        'manage users' => 'إدارة المستخدمين',
        'manage roles' => 'إدارة الأدوار والصلاحيات',

        'view clients' => 'عرض العملاء',
        'create clients' => 'إضافة عملاء',
        'edit clients' => 'تعديل العملاء',
        'delete clients' => 'حذف العملاء',

        'view transaction types' => 'عرض أنواع المعاملات',
        'create transaction types' => 'إضافة أنواع معاملات',
        'edit transaction types' => 'تعديل أنواع المعاملات',
        'delete transaction types' => 'حذف أنواع المعاملات',

        'view transactions' => 'عرض كل المعاملات',
        'view assigned transactions' => 'عرض المعاملات المسندة فقط',
        'create transactions' => 'إضافة معاملات',
        'edit transactions' => 'تعديل معاملات',
        'delete transactions' => 'حذف معاملات',
        'close transactions' => 'إغلاق / أرشفة معاملات',
        'change transaction status' => 'تغيير حالة المعاملة',

        'view attachments' => 'عرض المرفقات',
        'upload attachments' => 'رفع مرفقات',
        'review attachments' => 'مراجعة المرفقات',
        'delete attachments' => 'حذف المرفقات',

        'view archive' => 'عرض الأرشيف',
        'restore archive' => 'استرجاع من الأرشيف',

        'view contracts' => 'عرض العقود',
        'create contracts' => 'إضافة عقود',
        'edit contracts' => 'تعديل عقود',
        'delete contracts' => 'حذف عقود',

        'view payments' => 'عرض الدفعات',
        'create payments' => 'إضافة دفعات',
        'edit payments' => 'تعديل دفعات',
        'delete payments' => 'حذف دفعات',

        'view expenses' => 'عرض المصروفات',
        'create expenses' => 'إضافة مصروفات',
        'edit expenses' => 'تعديل مصروفات',
        'delete expenses' => 'حذف مصروفات',

        'view commissions' => 'عرض العمولات',
        'create commissions' => 'إضافة عمولات',
        'edit commissions' => 'تعديل عمولات',
        'delete commissions' => 'حذف عمولات',

        'view reports' => 'عرض التقارير',
        'view audit logs' => 'عرض سجل العمليات',

        'edit settings' => 'تعديل إعدادات النظام',
    ];
@endphp

<div class="row g-3">
    @foreach($permissionGroups as $groupName => $groupPermissions)
        @php
            $availablePermissions = collect($groupPermissions)
                ->filter(fn($permissionName) => $permissions->contains('name', $permissionName));
        @endphp

        @if($availablePermissions->isNotEmpty())
            <div class="col-lg-6">
                <div class="nk-permission-group">
                    <h6 class="nk-permission-group-title">
                        {{ $groupName }}
                    </h6>

                    <div class="row g-2">
                        @foreach($availablePermissions as $permissionName)
                            <div class="col-md-6">
                                <label class="nk-permission-item">
                                    <input type="checkbox"
                                           name="permissions[]"
                                           value="{{ $permissionName }}"
                                           class="form-check-input"
                                           @checked($selectedPermissions->contains($permissionName))>

                                    <span>
                                        {{ $permissionLabels[$permissionName] ?? $permissionName }}
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>