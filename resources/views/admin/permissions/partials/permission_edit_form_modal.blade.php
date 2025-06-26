
<div id="perm{{$perm->id}}0" class="modal fade" tabindex="-1">
                    <div class="modal-dialog modal-sm mt-lg-5">
                        <div class="modal-content">
                          <div class="modal-header">
                                <span class="font-weight-semibold modal-title">
                                    {{ __('general.edit') }} {{ __('auth.permission') }} 
                                </span>
                                <button aria-label="Close" class="btn-close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <form action="{{ route('permissions.permission') }}" class="feeForm{{$perm->id}}" method="POST">
                       
                            <div class="modal-body">
                                
                                <div class="form-group">
                                    @csrf
                                    <label>
                                        <i class="icon-unlocked mr-2"></i>
                                        {{ __('auth.permission').' '.__('general.name')}}
                                    </label>
                                    
                                    <input type="text" name="perm_name" value="{{ $perm->name }}" class="form-control" placeholder="" required readonly>
                                    
                                    <label class="form-group-float-label mt-2">Description</label>
                                    <input type="text" name="description" value="{{ $perm->description }}" class="form-control" placeholder="" required>
                                    <input type="hidden" name="id" class="row bg-whiteid" value="{{ $perm->id }}">
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button data-dismiss="modal" type="button"  class="btn bg-dark btn-warning btn-sm">{{ __('general.close')}}</button>
                                <button  type="submit" class="btn btn-sm btn-success">
                                 <i class="icon-plus-circle2 mr-2"></i>
                                 {{ __('general.update')}} {{ __('auth.permission')}}
                                </button>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>

