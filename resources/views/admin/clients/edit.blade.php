<x-app-layout>
    <div class="mb-4">
        <h1 class="nk-page-title">تعديل بيانات العميل</h1>
        <p class="nk-page-subtitle">تحديث بيانات العميل أو المنشأة.</p>
    </div>

    <div class="nk-card">
        <form method="POST" action="{{ route('admin.clients.update', $client) }}">
            @method('PUT')
            @include('admin.clients._form')
        </form>
    </div>
</x-app-layout>