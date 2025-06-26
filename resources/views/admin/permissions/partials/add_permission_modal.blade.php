<div class="modal fade" id="addPermission">
            <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title text-dark"> <i class="fa fa-user-o text-danger"></i>   {{ __('general.add') }} {{ __('auth.permission') }}</h6>
                        <button aria-label="Close" class="btn-close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                    </div>
             <form class="form-horizontal"  action="{{ route('permissions.permission') }}" method="POST" class="feeFormperm">
                
            <div class="modal-body">
                      
                @csrf    
                <div class="row bg-white">
                    <div class="col-md-12">
                        <div class="form-group form-group-float">
                                    <label class="form-group-float-label"> {{ __('auth.permission') }}</label>
                                    <input type="search" name="perm_name"  class="form-control" placeholder="{{ __('general.enter') }} {{ __('auth.permission') }}" required/>
                         </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group form-group-float">
                                    <label class="form-group-float-label">Description</label>
                                    <input type="search" name="description"  class="form-control" placeholder="Description" required/>
                         </div>
                    </div>
               </div>
                    </div>
                    <div class="modal-footer">
                       
                    <div class="col-md-6">
                            <button type="submit"  class="btn btn-success ">
                                <i class="icon-floppy-disk mr-2"></i>
                                {{ __('general.save') }} {{ __('auth.permission') }}
                            </button>
                            <button type="reset" class="btn btn-secondary reset">
                                <i class="icon-cross3 mr-2"></i>
                                {{ __('general.reset') }}
                            </button>
                    </div>
                    </div>
                    
                 </form>

                </div>
            </div>
        </div>
