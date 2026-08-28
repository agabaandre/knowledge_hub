<div id="user{{$user->id}}0" class="modal fade">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                         <form action="{{ route('permissions.userrole') }}" class="feeFormuser{{$user->id}}" method="POST">
                            <div class="modal-header">
                                <span class="font-weight-semibold modal-title">
                                    {{ __('general.edit') }} <span class="text-success"> {{$user->name }}'s</span>  {{ __('auth.role') }} 
                                </span>
                                         <button aria-label="Close" class="btn-close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                            </div>

                            <div class="modal-body text-left">
                                    @php
                                        $hubOwnerCountry = function_exists('hub_owner_country') ? hub_owner_country() : null;
                                        $hubOwnerCountryId = $hubOwnerCountry?->id ?? (function_exists('hub_owner_country_id') ? hub_owner_country_id() : null);
                                        $isCountryHub = function_exists('hub_is_country_portal') && hub_is_country_portal();
                                    @endphp
                                    <div class="form-group ">
                                        <label class="text-bold">
                                            <i class="icon-collaboration mr-2"></i>
                                            Access Level
                                        </label>
                                        <select class="form-control form-control-select2 select js-access-level-select" name="level_id" data-fouc>
                                           <option value="">Choose Level</option>
                                            @foreach($levels as $level)
                                            <option value="{{ $level->id }}" data-level-name="{{ $level->level_name }}" {{ ($level->id == @$user->access_level_id)?'selected':'' }}>{{ strtoupper($level->level_name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                <div class="form-group js-user-role-group" id="user_edit_role_group_{{ $user->id }}">
                                        @csrf
                                        <label class="text-bold">
                                            <i class="icon-collaboration mr-2"></i>
                                            {{ __('auth.role') }}
                                        </label>
                                        <select class="form-control form-control-select2 select js-user-role-select" name="role_id" data-fouc>
                                            @if(empty(@$userRole->id))
                                            <option value="" selected>Choose Role</option>
                                            @endif
                                            @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ ($role->id == @$userRole->id)?'selected':'' }}>{{ strtoupper($role->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="row">
                                    <div class="form-group col-md-6  col-sm-6">
                                        <label class="text-bold">
                                            <i class="icon-collaboration mr-2"></i>
                                            Country
                                        </label>
                                        @if ($isCountryHub && $hubOwnerCountryId)
                                            <input type="hidden" name="country_id" value="{{ $hubOwnerCountryId }}">
                                            <input type="text" class="form-control" value="{{ $hubOwnerCountry?->name ?? ('#'.$hubOwnerCountryId) }}" readonly>
                                        @else
                                            @include('partials.countries.dropdown',['selected'=>@$user->country_id])
                                        @endif
                                    </div>

                                    <input name="user_id" value="{{$user->id}}" type="hidden">

                                    <div class="form-group col-md-6">
                                            <label class="text-bold">
                                                <i class="icon-collaboration mr-2"></i>
                                                Author Account
                                            </label>
                                            @include('partials.authors.dropdown',['allfield'=>'None','selected'=>$user->author_id])
                                    </div>
                                    </div>


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
