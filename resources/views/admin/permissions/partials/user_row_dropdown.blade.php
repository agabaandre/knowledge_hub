<div class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-menu"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-right">
    
        <a href="#user{{$user->id}}0" data-toggle="modal" class="dropdown-item">
        {{ __('general.change') }}  {{ __('auth.role') }} </a>

    <a href="#login_state{{$user->id}}0" data-toggle="modal" class="dropdown-item">Change Login Status</i> 
    
    <a href="{{ url('profile', $user->id)}}" class="dropdown-item">
        {{ __('general.details') }}  </a>
    
    <a href="#delete{{$user->id}}0" data-toggle="modal" class="dropdown-item text-danger">
        Delete </a>
    </div>
</div>