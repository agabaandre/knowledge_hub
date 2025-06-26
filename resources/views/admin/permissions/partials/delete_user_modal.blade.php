<div id="delete{{$user->id}}0" class="modal fade" tabindex="-1">
                    <div class="modal-dialog modal-sm mt-lg-5">
                        <div class="modal-content">
                      <form action="{{ route('permissions.delete') }}"  method="POST">
                            <div class="modal-header">
                                <span class="font-weight-semibold modal-title">
                                    {{ __('general.delete') }} <span class="text-success"> {{$user->name }}'s</span>  Account ? 
                                </span>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>

                            <div class="modal-body">
                                
                                        @csrf
                                     <h2>Warning: This action is permanent!</h2>
           
                                    <input name="user_id" value="{{$user->id}}" type="hidden">
                            </div>

                            <div class="modal-footer">
                                <button data-dismiss="modal" type="button"  class="btn bg-dark btn-warning btn-sm">{{ __('general.close')}}</button>
                                <button type="submit" class="btn btn-sm btn-danger">
                                 <i class="icon-trash mr-2"></i>
                                 {{ __('general.delete')}} Account
                                </button>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>