@if(session('status'))
    <p class="admin-notice" role="status">{{ session('status') }}</p>
@endif
@if($errors->any())
    <div class="admin-notice admin-notice--error" role="alert">
        <p>Periksa kembali isian berikut:</p>
        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
