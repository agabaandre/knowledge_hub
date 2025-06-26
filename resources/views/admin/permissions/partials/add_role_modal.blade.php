<div id="addRole" class="modal fade" tabindex="-1">
                    <div class="modal-dialog modal-sm mt-lg-5">
                        <div class="modal-content">
                           <div class="modal-header">
                                <span class="font-weight-semibold modal-title">
                                    {{ __('general.add') }} {{ __('auth.role') }} 
                                </span>
                                <button  type="button" aria-label="Close" class="btn-close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button></div>
                            <form action="{{ route('permissions.role') }}" method="POST">
                      
                            <div class="modal-body">
                                
                               @csrf    
                                <div class="row bg-white">
                                    <div class="col-md-12">
                                        <div class="form-group form-group-float">
                                                    <label class="form-group-float-label"> {{ __('auth.role') }}</label>
                                                    <input type="text" name="role_name"  class="form-control" placeholder="{{ __('general.enter') }} {{ __('auth.role') }}"  required/>
                                        </div>
                                        
                                    </div>
                                    
                            </div>

                            </div>

                            <div class="modal-footer">
                                <div class="col-md-12">
                                            <button type="reset" class="btn btn-secondary  reset">
                                                <i class="icon-cross3 mr-2"></i>
                                                {{ __('general.reset') }}
                                            </button>
                                            
                                            <button type="submit" class="btn btn-success">
                                                <i class="icon-floppy-disk mr-2"></i>
                                                {{ __('general.save') }} {{ __('auth.role') }}
                                            </button>
                                </div>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>

