<x-app-layout>
    <div class="mb-4">
        <h1 class="nk-page-title">لوحة التحكم</h1>
        <p class="nk-page-subtitle">
            نظرة عامة على المعاملات، العقود، الدفعات، والمستندات داخل النظام.
        </p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="nk-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="nk-stat-label">المعاملات تحت الإجراء</div>
                        <div class="nk-stat-number">0</div>
                    </div>
                    <div class="nk-stat-icon">
                        <i class="bi bi-folder2-open"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="nk-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="nk-stat-label">العقود النشطة</div>
                        <div class="nk-stat-number">0</div>
                    </div>
                    <div class="nk-stat-icon">
                        <i class="bi bi-file-earmark-ruled"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="nk-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="nk-stat-label">إجمالي الدفعات</div>
                        <div class="nk-stat-number">0</div>
                    </div>
                    <div class="nk-stat-icon">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="nk-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="nk-stat-label">مرفقات ناقصة</div>
                        <div class="nk-stat-number">0</div>
                    </div>
                    <div class="nk-stat-icon">
                        <i class="bi bi-paperclip"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="nk-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold text-success mb-1">آخر المعاملات</h5>
                <p class="text-muted mb-0 small">سيتم عرض أحدث المعاملات هنا بعد إضافة أول معاملة.</p>
            </div>

            @can('create transactions')
                <a href="#" class="nk-btn-main">
                    <i class="bi bi-plus-circle"></i>
                    إضافة معاملة
                </a>
            @endcan
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>رقم المعاملة</th>
                        <th>العميل</th>
                        <th>نوع المعاملة</th>
                        <th>الحالة</th>
                        <th>المسؤول</th>
                        <th>تاريخ الإنشاء</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            لا توجد معاملات حتى الآن
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>