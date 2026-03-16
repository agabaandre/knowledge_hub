@if(Session::has('alert') || Session::has('message'))
<div class="alert alert-{{ Session::get('alert_class') ?? 'info' }} alert-dismissible fade show" role="alert">
    {!! Session::get('alert') ?? Session::get('message') !!}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if(isset($errors) && $errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <span class="fw-bold">{!! implode('', $errors->all('<div>:message</div>')) !!}</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
