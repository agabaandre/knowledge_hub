<div id="login_state{{$user->id}}0" class="modal fade" tabindex="-1">
                    <div class="modal-dialog modal-sm mt-lg-5">
                        <div class="modal-content">
                      <form action="{{ route('permissions.reset') }}"  method="POST">
                            <div class="modal-header">
                                <span class="font-weight-semibold modal-title">
                                    {{ __('general.edit') }} <span class="text-success"> {{$user->name }}'s</span>  {{ __('auth.login_status') }} 
                                </span>
                                         <button aria-label="Close" class="btn-close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                            </div>

                            <div class="modal-body">
                                
                                <div class="form-group ">
                                        @csrf
                                        <label class="text-bold">
                                            <i class="icon-collaboration mr-2"></i>
                                            {{ __('general.select') }} {{ __('auth.login_status') }}
                                        </label>
                                       
                                        <select class="form-control form-control-select2 select" name="status" required="">
                                          <option value="" selected disbaled>Select Status</option>
                                            @if($user->status ==1)
                                              <option  value="3">Reset Password</option>
                                              <option value="0">Block user</option>
                                              <option value="2">Restrict</option>
                                            @endif
                                            @if($user->status !==1)
                                              @if($user->status !==3)
                                              <option  value="1">Activate User</option>
                                              @endif
                                              @if($user->status !==0)
                                              <option value="0">Block user</option>
                                              @endif
                                              <option value="3">Reset Password</option>
                                            @endif
                                        </select> 
                                    </div>
                                    <input name="user_id" value="{{$user->id}}" type="hidden">

                            </div>

                            <div class="modal-footer">
                                <button data-dismiss="modal" type="button"  class="btn bg-dark btn-warning btn-sm">{{ __('general.close')}}</button>
                                <button type="submit" class="btn btn-sm btn-success">
                                 <i class="icon-plus-circle2 mr-2"></i>
                                 {{ __('general.update')}} {{ __('auth.login_status')}}
                                </button>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>

