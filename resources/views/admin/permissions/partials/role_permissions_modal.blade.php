<div id="perms{{$role->id}}0" class="modal fade" tabindex="-1">
                    <div class="modal-dialog modal-lg mt-lg-5">
                        <div class="modal-content">
                      <form action="{{ route('permissions.torole') }}" class="feeFormperms{{$role->id}} bg-white" method="POST">
                            <div class="modal-header">
                                <span class="font-weight-semibold modal-title">
                                     {{ strtoupper($role->name) }}   {{ __('auth.permissions') }} 
                                </span>
                                <button aria-label="Close" class="btn-close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>  </div>

                            <div class="modal-body">
                                
                              @csrf
                              
                              <input type="hidden" name="role_id" class="row bg-whiteid" value="{{ $role->id }}">
                            <div class="row bg-white">
                              @foreach($permissions as $perm)
                                <div class="form-check col-md-4" style="padding: 13px;">
	                                    <label class="">
	                                        <input  type="checkbox"  name="permissions[]"  value="{{$perm->id}}" style="width:25px;height:25px;top:10px;"
	                                        {{ in_array($perm->id,$rolePerms)?'checked':'' }} />
	                                         <small >{{ strtoupper($perm->description) }}</small>
	                                    </label>
	                            </div>
                              @endforeach
                            </div>

                            </div>

                            <div class="modal-footer">
                                <button data-dismiss="modal" type="button"  class="btn bg-dark btn-warning btn-sm">{{ __('general.close')}}</button>
                                @php $formRef = 'perms'.$role->id; @endphp
                                <button type="submit" class="btn btn-sm btn-success">
                                 <i class="icon-plus-circle2 mr-2"></i>
                                 {{ __('general.update')}} {{ __('auth.permissions')}}
                                </button>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>

