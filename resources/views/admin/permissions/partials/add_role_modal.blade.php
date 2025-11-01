<div id="addRole" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <span class="font-weight-semibold modal-title">
                    {{ __('general.add') }} {{ __('auth.role') }} 
                </span>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('permissions.role') }}" method="POST">
                <div class="modal-body">
                    @csrf    
                    <div class="form-group">
                        <label class="form-label">{{ __('auth.role') }} <span class="text-danger">*</span></label>
                        <input type="text" name="role_name" class="form-control form-control-sm" placeholder="{{ __('general.enter') }} {{ __('auth.role') }}" required/>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        {{ __('general.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fa fa-save mr-1"></i>
                        {{ __('general.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
