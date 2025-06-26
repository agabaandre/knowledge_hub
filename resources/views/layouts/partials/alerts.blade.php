@if(Session::has('alert') || Session::has('message'))

<div class="row mt-2">
<div class="col-lg-12 alert alert-{{ Session::get('alert_class') ?? 'info' }} alert-dismissible fade show" style="min-width:100%;" role="alert">
 <span class="fw-bold">{!! Session::get('alert') ??  Session::get('message') !!}</span>
</div>
</div>
@endif

@if($errors->any())
<div class="row mt-2">
<div class="col-lg-12 alert alert-danger alert-dismissible fade show" style="min-width:100%;" role="alert">
<span class="fw-bold">{!! implode('', $errors->all('<div>:message</div>')) !!}</span>
</div>
</div>
@endif

 
