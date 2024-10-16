<div id="trail{{$row->id}}0" class="modal fade" tabindex="-1">
                    <div class="modal-dialog modal-lg mt-lg-5">
                        <div class="modal-content">
                       <div class="modal-header">
                                <span class="font-weight-semibold modal-title">
                                    Trail Details
                                </span>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>

                            <div class="modal-body">
                                <div class="row">
                                 <div class="form-group col-lg-6">
                                       <b>Date: </b>
                                       <label>
                                       {{ $row->created_at }}
                                       </label>
                                   </div>
                                   
                                   <div class="form-group col-lg-6">
                                       <b>User: </b>
                                       <label>
                                       {{ $row->user->name }}
                                       </label>
                                   </div>

                                   <div class="form-group col-lg-12">
                                       <b>Action: </b>
                                       <label>
                                       {{ $row->action }}
                                       </label>
                                   </div>
                                    @if(@$row->old_data)
                                    <div class="form-group col-lg-6">
                                    <label><b>Old data:</b></label>
                                        @php
                                          $new = json_decode($row->old_data, true)
                                        @endphp
                                        
                                       <pre style="text-align: left!important;"> 
                                           {{ $row->old_data }}
                                       </pre>
                                    </div>
                                    @endif
                                    @if(@$row->new_data)
                                    <div class="form-group col-lg-6">
                                    <label><b>New data:</b></label>
                                        @php
                                          $new = json_decode($row->new_data, true)
                                        @endphp
                                        
                                       <pre style="text-align: left!important;"> 
                                           {{ $row->new_data }}
                                       </pre>
                                    </div>
                                    @endif
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button data-dismiss="modal" type="button"  class="btn bg-dark btn-warning btn-sm">{{ __('general.close')}}</button>
                             
                            </div>
                        </form>
                        </div>
                    </div>
                </div>