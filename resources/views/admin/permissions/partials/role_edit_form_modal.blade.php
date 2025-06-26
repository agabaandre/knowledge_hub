<div id="role{{$role->id}}0" class="modal fade" tabindex="-1">
                    <div class="modal-dialog modal-sm mt-lg-5">
                        <div class="modal-content">
                      <form action="{{ route('permissions.role') }}" class="feeForm{{$role->id}}" method="POST">
                            <div class="modal-header">
                                <span class="font-weight-semibold modal-title">
                                    {{ __('general.edit') }} {{ __('auth.role') }} 
                                </span>
                                <button aria-label="Close" class="btn-close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button></div>

                            <div class="modal-body">
                                
                                <div class="form-group">
                                    <br>
                                    @csrf
                                    <label>
                                        <i class="icon-collaboration mr-2"></i>
                                        {{ __('auth.role').' '.__('general.name')}}
                                    </label>
                                    <input type="text" name="role_name" value="{{ $role->name }}" class="form-control" placeholder="" required>
                                    <input type="hidden" name="row bg-whiteid" class="row bg-whiteid" value="{{ $role->id }}">
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button data-dismiss="modal" type="button"  class="btn bg-dark btn-warning btn-sm">{{ __('general.close')}}</button>
                            
                                <button type="submit" class="btn btn-sm btn-success">
                                 <i class="icon-plus-circle2 mr-2"></i>
                                 {{ __('general.update')}} {{ __('auth.role')}}
                                </button>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>

