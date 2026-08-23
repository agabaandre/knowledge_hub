@php
    $prefix = $prefix ?? 'add';
    $permissions = $categoryPermissions ?? collect();
@endphp
<div class="col-md-12">
    <div class="form-check mb-2">
        <input type="checkbox" class="form-check-input" value="1" name="show_menu" id="{{ $prefix }}_show_menu">
        <label class="form-check-label" for="{{ $prefix }}_show_menu">Show on menu</label>
    </div>
    <div class="form-check mb-2">
        <input type="checkbox" class="form-check-input" value="1" name="is_special" id="{{ $prefix }}_is_special">
        <label class="form-check-label" for="{{ $prefix }}_is_special">Requires login (special / custom URL)</label>
    </div>
    <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" value="1" name="is_restricted" id="{{ $prefix }}_is_restricted">
        <label class="form-check-label" for="{{ $prefix }}_is_restricted">Restricted — signed-in users need a permission (e.g. Workforce)</label>
    </div>
    <div class="mb-3">
        <label class="form-label" for="{{ $prefix }}_required_permission">Required permission</label>
        @if($permissions->isNotEmpty())
            <select class="form-control" name="required_permission" id="{{ $prefix }}_required_permission">
                <option value="">None</option>
                @foreach($permissions as $permissionName)
                    <option value="{{ $permissionName }}">{{ $permissionName }}</option>
                @endforeach
            </select>
        @else
            <input type="text" class="form-control" name="required_permission" id="{{ $prefix }}_required_permission" placeholder="view_workforce">
        @endif
        <small class="text-muted">If Restricted is checked and this is empty, <code>view_workforce</code> is used. Assigned to Admin and RCC Admin by default.</small>
    </div>
</div>
