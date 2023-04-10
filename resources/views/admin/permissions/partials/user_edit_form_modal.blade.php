<div id="user{{$user->id}}0" class="modal fade" tabindex="-1">
                    <div class="modal-dialog modal-sm mt-lg-5">
                        <div class="modal-content">
                      <form action="{{ route('permissions.userrole') }}" class="feeFormuser{{$user->id}}" method="POST">
                            <div class="modal-header">
                                <span class="font-weight-semibold modal-title">
                                    {{ __('general.edit') }} <span class="text-success"> {{$user->name }}'s</span>  {{ __('auth.role') }} 
                                </span>
                                         <button aria-label="Close" class="btn-close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                            </div>

                            <div class="modal-body">
                                
                                <div class="form-group ">
                                        @csrf
                                        <label class="text-bold">
                                            <i class="icon-collaboration mr-2"></i>
                                            {{ __('auth.role') }}
                                        </label>
                                        <select class="form-control form-control-select2 select" name="role_id" data-fouc readonly>
                                            @if(empty(@$userRole->id))
                                            <option>Choose Role</option>
                                            @endif
                                            @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ ($role->id == @$userRole->id)?'selected':'' }}>{{ strtoupper($role->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <input name="user_id" value="{{$user->id}}" type="hidden">


                            </div>

                            <div class="modal-footer">
                                <button data-dismiss="modal" type="button"  class="btn bg-dark btn-warning btn-sm">{{ __('general.close')}}</button>
                            
                                <button type="submit" class="btn btn-sm btn-success" >
                                 <i class="icon-plus-circle2 mr-2"></i>
                                 {{ __('general.update')}} {{ __('auth.role')}}
                                </button>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>

