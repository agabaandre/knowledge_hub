@if(Session::has('alert'))
    <div class="alert alert-{{ Session::get('alert_class') ?? 'info' }} mt-3" role="alert">
        {!! Session::get('alert') !!}
        <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-hidden="true">&times;</button>
    </div>
@endif