<x-app-layout>
    <div class="mb-4">
        <h1 class="nk-page-title">إضافة عميل جديد</h1>
        <p class="nk-page-subtitle">تسجيل بيانات العميل أو المنشأة لاستخدامها لاحقًا داخل المعاملات.</p>
    </div>

    <div class="nk-card">
        <form method="POST" action="{{ route('admin.clients.store') }}">
            @include('admin.clients._form')
        </form>
    </div>
</x-app-layout>