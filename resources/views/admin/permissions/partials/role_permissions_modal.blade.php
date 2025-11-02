<div id="perms{{$role->id}}0" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-xl mt-lg-5">
                        <div class="modal-content">
                      <form action="{{ route('permissions.torole') }}" class="feeFormperms{{$role->id}} bg-white" method="POST">
                            <div class="modal-header">
                                <span class="font-weight-semibold modal-title">
                        {{ strtoupper($role->name) }} {{ __('auth.permissions') }} 
                                </span>
                    <button aria-label="Close" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    @csrf
                    <input type="hidden" name="role_id" value="{{ $role->id }}">
                    
                    @php
                        // Group permissions by module
                        $groupedPermissions = [];
                        foreach($permissions as $perm) {
                            // Extract group from permission name (part before first underscore)
                            $nameParts = explode('_', $perm->name);
                            $group = ucfirst($nameParts[0] ?? 'General');
                            
                            // Map to more user-friendly group names
                            $groupMapping = [
                                'View' => 'View & Access',
                                'Manage' => 'Manage',
                                'Create' => 'Create',
                                'Update' => 'Update',
                                'Delete' => 'Delete',
                                'Add' => 'Add',
                                'Moderate' => 'Moderation',
                                'Modify' => 'Modify',
                            ];
                            
                            $displayGroup = $groupMapping[$group] ?? $group;
                            
                            // Further categorize by module type
                            $module = 'General';
                            if (stripos($perm->name, 'publication') !== false || stripos($perm->name, 'resource') !== false) {
                                $module = 'Publications & Resources';
                            } elseif (stripos($perm->name, 'forum') !== false) {
                                $module = 'Forums';
                            } elseif (stripos($perm->name, 'content_request') !== false || stripos($perm->name, 'content-request') !== false) {
                                $module = 'Content Requests';
                            } elseif (stripos($perm->name, 'kpi') !== false || stripos($perm->name, 'dashboard') !== false || stripos($perm->name, 'performance') !== false) {
                                $module = 'Dashboard & KPIs';
                            } elseif (stripos($perm->name, 'expert') !== false) {
                                $module = 'Experts';
                            } elseif (stripos($perm->name, 'fact') !== false) {
                                $module = 'Facts';
                            } elseif (stripos($perm->name, 'quote') !== false) {
                                $module = 'Quotes';
                            } elseif (stripos($perm->name, 'quiz') !== false || stripos($perm->name, 'quize') !== false) {
                                $module = 'Quiz';
                            } elseif (stripos($perm->name, 'event') !== false) {
                                $module = 'Events';
                            } elseif (stripos($perm->name, 'file_type') !== false || stripos($perm->name, 'filetype') !== false) {
                                $module = 'File Types';
                            } elseif (stripos($perm->name, 'source') !== false || stripos($perm->name, 'author') !== false) {
                                $module = 'Sources & Authors';
                            } elseif (stripos($perm->name, 'theme') !== false) {
                                $module = 'Themes';
                            } elseif (stripos($perm->name, 'tag') !== false) {
                                $module = 'Tags';
                            } elseif (stripos($perm->name, 'faq') !== false) {
                                $module = 'FAQs';
                            } elseif (stripos($perm->name, 'geo') !== false || stripos($perm->name, 'coverage') !== false) {
                                $module = 'Geographical Coverage';
                            } elseif (stripos($perm->name, 'asset') !== false) {
                                $module = 'Assets';
                            } elseif (stripos($perm->name, 'privacy') !== false) {
                                $module = 'Privacy Policy';
                            } elseif (stripos($perm->name, 'rcc') !== false) {
                                $module = 'RCC';
                            } elseif (stripos($perm->name, 'mailing') !== false) {
                                $module = 'Mailing List';
                            } elseif (stripos($perm->name, 'user') !== false) {
                                $module = 'Users & Permissions';
                            } elseif (stripos($perm->name, 'commsofpractice') !== false || stripos($perm->name, 'community') !== false || stripos($perm->name, 'cop') !== false) {
                                $module = 'Communities of Practice';
                            }
                            
                            if (!isset($groupedPermissions[$module])) {
                                $groupedPermissions[$module] = [];
                            }
                            if (!isset($groupedPermissions[$module][$displayGroup])) {
                                $groupedPermissions[$module][$displayGroup] = [];
                            }
                            $groupedPermissions[$module][$displayGroup][] = $perm;
                        }
                        
                        // Sort modules alphabetically
                        ksort($groupedPermissions);
                    @endphp
                    
                    @foreach($groupedPermissions as $module => $groups)
                        <div class="permission-module mb-4">
                            <h6 class="text-primary font-weight-bold mb-2" style="font-size: 0.95rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px;">
                                <i class="fa fa-folder-open mr-2"></i>{{ $module }}
                            </h6>
                            
                            @foreach($groups as $group => $perms)
                                <div class="permission-group mb-3">
                                    <h6 class="text-muted font-weight-semibold mb-2" style="font-size: 0.85rem; margin-left: 10px;">
                                        {{ $group }}
                                    </h6>
                                    <div class="row" style="margin-left: 5px; margin-right: 5px;">
                                        @foreach($perms as $perm)
                                            <div class="col-md-6 col-lg-4" style="padding: 5px 10px;">
                                                <label class="permission-checkbox-label" style="margin-bottom: 0; cursor: pointer; display: flex; align-items: center;">
                                                    <input type="checkbox" 
                                                           name="permissions[]" 
                                                           value="{{$perm->id}}" 
                                                           style="width: 16px; height: 16px; margin-right: 6px; cursor: pointer; flex-shrink: 0;"
                                                           {{ in_array($perm->id, $rolePerms) ? 'checked' : '' }} />
                                                    <span style="font-size: 0.8rem; line-height: 1.3; color: #333;">
                                                        {{ $perm->description ?? ucwords(str_replace('_', ' ', $perm->name)) }}
                                                    </span>
	                                    </label>
	                            </div>
                              @endforeach
                            </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                            </div>

                <div class="modal-footer d-flex justify-content-between">
                    <button data-dismiss="modal" type="button" class="btn btn-secondary btn-sm">{{ __('general.close')}}</button>
                                <button type="submit" class="btn btn-sm btn-success">
                                 <i class="icon-plus-circle2 mr-2"></i>
                                 {{ __('general.update')}} {{ __('auth.permissions')}}
                                </button>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>

<style>
    .permission-module {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 4px;
        border-left: 3px solid #007bff;
    }
    
    .permission-group {
        background: #ffffff;
        padding: 8px;
        border-radius: 3px;
        margin-left: 5px;
    }
    
    .permission-checkbox-label:hover {
        background-color: #f0f0f0;
        border-radius: 3px;
    }
    
    .permission-checkbox-label input[type="checkbox"]:checked + span {
        font-weight: 600;
        color: #007bff;
    }
    
    /* Scrollbar styling for modal body */
    .modal-body::-webkit-scrollbar {
        width: 8px;
    }
    
    .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    .modal-body::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }
    
    .modal-body::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
