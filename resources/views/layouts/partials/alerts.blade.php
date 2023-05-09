@if(Session::has('alert'))

<div class="alert alert-{{ Session::get('alert_class') ?? 'info' }} alert-dismissible fade show" role="alert">
 <span class="fw-bold">{!! Session::get('alert') !!}</span>
</div>
   
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
<span class="fw-bold">{!! implode('', $errors->all('<div>:message</div>')) !!}</span>
</div>
@endif

 
