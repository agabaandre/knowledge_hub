@extends(admin_layout())
@section('styles')
<style>
.users-page .user-card {
    border: 1px solid #e2e8f0;
    border-radius: .5rem;
    background: #fff;
    margin-bottom: 1.25rem;
    overflow: hidden;
}
.users-page .user-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f1f5f9;
    flex-wrap: wrap;
}
.users-page .user-card-identity {
    display: flex;
    align-items: center;
    gap: .875rem;
    min-width: 0;
}
.users-page .user-card-avatar {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid #e2e8f0;
    flex-shrink: 0;
    background: #f8fafc;
}
.users-page .user-card-name {
    font-weight: 700;
    font-size: 1.05rem;
    color: #1e40af;
    line-height: 1.25;
    word-break: break-word;
}
.users-page .user-card-meta {
    font-size: .8rem;
    color: #64748b;
    margin-top: .2rem;
}
.users-page .user-card-body {
    padding: 1rem 1.25rem 1.25rem;
}
.users-page .user-card-cols {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.25rem 1.5rem;
}
.users-page .user-card-section-title {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .04em;
    color: #94a3b8;
    text-transform: uppercase;
    margin-bottom: .5rem;
}
.users-page .user-card-section p {
    margin: 0 0 .35rem;
    font-size: .875rem;
    color: #334155;
    line-height: 1.45;
    word-break: break-word;
}
.users-page .user-card-section p:last-child { margin-bottom: 0; }
.users-page .user-card-actions {
    padding: 0 1.25rem 1.25rem;
    border-top: 1px solid #f1f5f9;
    padding-top: 1rem;
    margin-top: 0;
}
.users-page .user-card-actions .btn-group { flex-wrap: wrap; }
.users-page .user-card-actions .btn { margin-bottom: .35rem; }
/* Bootstrap pagination — match prior DataTables paginate look */
.users-page .pagination { margin-bottom: 0; flex-wrap: wrap; justify-content: flex-end; }
.users-page .pagination .page-link {
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #1f2937;
    padding: .25rem .5rem;
    margin: 0 .125rem;
    border-radius: .25rem;
}
.users-page .pagination .page-link:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #1f2937;
}
.users-page .pagination .page-item.active .page-link {
    background: #1f2937;
    border-color: #1f2937;
    color: #fff;
}
.users-page .pagination .page-item.disabled .page-link {
    color: #9ca3af;
    background: #fff;
}
</style>
@endsection

@section('content')

@include('common.table')

<div class="users-page">
<div class="page-header">
    <h1 class="page-title">Users</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Users</li>
        </ol>
    </div>
</div>

@include('admin.permissions.partials.add_user_modal')

<div class="col-md-12 bg-white py-4 rounded">
    <div class="row">
        <div class="col-md-9">
            <h3 class="card-title mb-0">{{ __('auth.users') }}</h3>
        </div>
        <div class="col-md-3">
            <a class="modal-effect btn btn-outline-primary d-block d-grid mb-3 float-right" data-effect="effect-rotate-bottom" data-toggle="modal" href="#addUser"><i class="fa fa-plus-circle"></i> {{ __('general.add') }} {{ __('auth.user') }}</a>
        </div>
    </div>

    <form id="user-filters" action="{{ route('permissions.users') }}" method="GET">
        <div class="row bg-white pb-3">
            <div class="form-group col-md-12">
                <label>Search</label>
                <input type="text" name="term" value="{{ old('term', $search->term ?? '') }}" class="form-control" placeholder="Search by name, email, or phone">
            </div>
            <div class="form-group col-md-3">
                <label>Africa CDC Staff</label>
                <select name="is_staff" id="filter_staff" class="form-control">
                    <option value="" {{ ($search->is_staff ?? '') === '' || $search->is_staff === null ? 'selected' : '' }}>All</option>
                    <option value="1" {{ ($search->is_staff ?? '') === '1' ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ ($search->is_staff ?? '') === '0' ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label>Verified</label>
                <select name="verified" id="filter_verified" class="form-control">
                    <option value="" {{ ($search->verified ?? '') === '' || $search->verified === null ? 'selected' : '' }}>All</option>
                    <option value="1" {{ ($search->verified ?? '') === '1' ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ ($search->verified ?? '') === '0' ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div class="form-group col-md-2">
                <label>Per page</label>
                <select name="count" class="form-control">
                    @foreach([10, 20, 50, 100] as $n)
                        <option value="{{ $n }}" {{ (int)($search->count ?? 20) === $n ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mt-4">
                <button type="submit" class="btn btn-dark"><i class="icon-filter4"></i> {{ __('general.search') }} {{ __('general.users') }}</button>
            </div>
        </div>
    </form>

    <hr>

    @if($users->count() > 0)
        @php
            $statuses = [0 => 'Inactive', 2 => 'Restricted', 3 => 'Reset', 1 => 'Active'];
        @endphp
        @foreach($users as $u)
            @php
                $effectiveLogin = $u->last_login_at ?? $u->access_last_at ?? null;
                $lastLoginLabel = $effectiveLogin ? \Carbon\Carbon::parse($effectiveLogin)->format('M d, Y H:i') : '—';
                if (!empty($u->is_photo_external) && (int) $u->is_photo_external === 1 && !empty($u->photo)) {
                    $avatarUrl = $u->photo;
                } elseif (!empty($u->photo)) {
                    $avatarUrl = asset('storage/uploads/users/'.$u->photo);
                } else {
                    $avatarUrl = asset('assets/images/user.jpg');
                }
                $statusLabel = $statuses[$u->status] ?? 'Inactive';
                $statusClass = ((int) $u->status === 1) ? 'badge-dark' : 'badge-secondary';
                $authType = !empty($u->is_social_login)
                    ? ('Social ('.($u->social_provider ? \App\Support\OAuthAccountSecurity::providerDisplayName($u->social_provider) : 'SSO').')')
                    : 'Email & password';
            @endphp
            <div class="user-card">
                <div class="user-card-header">
                    <div class="user-card-identity">
                        <img class="user-card-avatar" src="{{ $avatarUrl }}" alt="" width="52" height="52">
                        <div class="min-w-0">
                            <div class="user-card-name">{{ $u->name ?: trim(($u->first_name ?? '').' '.($u->last_name ?? '')) ?: '—' }}</div>
                            <div class="user-card-meta">ID: {{ $u->id }} @if($u->email)<span class="mx-1">·</span> {{ $u->email }}@endif</div>
                        </div>
                    </div>
                    <div>
                        <span class="badge {{ $statusClass }} px-3 py-2" style="border-radius: 999px;">{{ $statusLabel }}</span>
                    </div>
                </div>
                <div class="user-card-body">
                    <div class="user-card-cols">
                        <div class="user-card-section">
                            <div class="user-card-section-title">Assignment</div>
                            <p><strong>Job</strong><br>{{ $u->job_title ?: '—' }}</p>
                            <p><strong>Country</strong><br>{{ $u->country_name ?: '—' }}</p>
                            <p><strong>Access level</strong><br>{{ $u->access_level_name ?: '—' }}</p>
                        </div>
                        <div class="user-card-section">
                            <div class="user-card-section-title">Contact</div>
                            <p><strong>Work email</strong><br>{{ $u->email ?: '—' }}</p>
                            <p><strong>Phone</strong><br>{{ $u->phone_number ?: '—' }}</p>
                        </div>
                        <div class="user-card-section">
                            <div class="user-card-section-title">Account</div>
                            <p><strong>Verified</strong><br>{{ $u->email_verified_at ? 'Yes' : 'No' }}</p>
                            <p><strong>Sign-in</strong><br>{{ $authType }}</p>
                            <p><strong>Role</strong><br>{{ $u->role_name ? strtoupper($u->role_name) : 'N/A' }}</p>
                            <p><strong>Last login</strong><br>{{ $lastLoginLabel }}</p>
                        </div>
                    </div>
                </div>
                <div class="user-card-actions">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary btn-edit-user"
                            data-id="{{ $u->id }}"
                            data-first-name="{{ e($u->first_name ?? '') }}"
                            data-last-name="{{ e($u->last_name ?? '') }}"
                            data-name="{{ e($u->name ?? '') }}"
                            data-email="{{ e($u->email ?? '') }}"
                            data-phone="{{ e($u->phone_number ?? '') }}"
                            data-role-id="{{ $u->role_id ?? '' }}"
                            data-role="{{ e($u->role_name ?? '') }}"
                            data-country-id="{{ $u->country_id ?? '' }}"
                            data-administrative-unit-id="{{ $u->administrative_unit_id ?? '' }}"
                            data-author-id="{{ $u->author_id ?? '' }}"
                            data-level-id="{{ $u->access_level_id ?? '' }}"
                            data-verified="{{ $u->email_verified_at ? 1 : 0 }}"
                            data-status="{{ (int) $u->status }}">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-reset-user" data-id="{{ $u->id }}"><i class="fa fa-key"></i> Reset password</button>
                        @if(!$u->email_verified_at)
                            <button type="button" class="btn btn-outline-success btn-send-verify" data-id="{{ $u->id }}"><i class="fa fa-paper-plane"></i> Send verify email</button>
                            <button type="button" class="btn btn-outline-info btn-mark-verify" data-id="{{ $u->id }}"><i class="fa fa-check"></i> Mark verified</button>
                        @endif
                        <a href="{{ route('permissions.profile') }}?user={{ $u->id }}" class="btn btn-outline-secondary"><i class="fa fa-user"></i> Profile</a>
                        <button type="button" class="btn btn-outline-danger btn-delete-user" data-id="{{ $u->id }}"><i class="fa fa-trash"></i> Delete</button>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center pt-2 pb-4">
            <div class="text-muted small mb-2 mb-md-0">
                Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users
            </div>
            <div>
                {{ $users->onEachSide(1)->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-5 text-muted">No users found for these filters.</div>
    @endif
</div>

<form id="sendVerifyForm" method="POST" action="{{ route('permissions.sendverification') }}" style="display:none;">
    @csrf
    <input type="hidden" name="id" id="send_verify_user_id">
</form>
<form id="markVerifyForm" method="POST" action="{{ route('permissions.verifyuser') }}" style="display:none;">
    @csrf
    <input type="hidden" name="id" id="mark_verify_user_id">
</form>
</div>
@endsection

@section('scripts')
<script>
    $(function(){
        function viewerRoleVisibility($levelSelect) {
            if (!$levelSelect || !$levelSelect.length) return;
            var $form = $levelSelect.closest('form');
            var $group = $form.find('.js-user-role-group');
            var $role = $form.find('.js-user-role-select');
            if (!$group.length || !$role.length) return;
            var levelName = ($levelSelect.find('option:selected').attr('data-level-name') || '').toLowerCase();
            var isViewer = levelName === 'viewer';
            if (isViewer) {
                $group.hide();
                $role.prop('disabled', true).prop('required', false).val('').trigger('change');
                $form.find('.js-role-required-marker').hide();
            } else {
                $group.show();
                $role.prop('disabled', false);
                if ($role.attr('id') === 'edit_role_id') {
                    $role.prop('required', true);
                }
                $form.find('.js-role-required-marker').show();
            }
        }

        $(document).on('change select2:select', '.js-access-level-select', function () {
            viewerRoleVisibility($(this));
        });

        $('#addUser').on('shown.bs.modal', function () {
            viewerRoleVisibility($(this).find('.js-access-level-select'));
        });

        $(document).on('click', '.btn-edit-user', function(){
            var id = $(this).data('id');
            var firstName = $(this).data('first-name') || '';
            var lastName = $(this).data('last-name') || '';
            var name = $(this).data('name') || '';
            var email = $(this).data('email') || '';
            var phone = $(this).data('phone') || '';
            var roleId = $(this).data('role-id') || '';
            var countryId = $(this).data('country-id') || '';
            var administrativeUnitId = $(this).data('administrative-unit-id') || '';
            var authorId = $(this).data('author-id') || '';
            var levelId = $(this).data('level-id') || '';
            var verified = $(this).data('verified') || 0;
            var status = $(this).data('status') || 0;

            if (!firstName && !lastName && name) {
                var nameParts = name.trim().split(' ');
                lastName = nameParts.pop() || '';
                firstName = nameParts.join(' ') || '';
            }

            $('#edit_user_id').val(id);
            $('#edit_first_name').val(firstName);
            $('#edit_last_name').val(lastName);
            $('#edit_email').val(email);
            $('#edit_phone').val(phone);
            $('#edit_country_id').val(countryId).trigger('change');
            $('#edit_administrative_unit_id').val(administrativeUnitId).trigger('change');
            $('#edit_author_id').val(authorId).trigger('change');
            $('#edit_verified').prop('checked', !!verified);
            $('#edit_status').val(status);

            if ($('#edit_role_id').hasClass('select2-hidden-accessible')) {
                $('#edit_role_id').select2('destroy');
            }
            if ($('#edit_level_id').hasClass('select2-hidden-accessible')) {
                $('#edit_level_id').select2('destroy');
            }
            if ($('#edit_country_id').hasClass('select2-hidden-accessible')) {
                $('#edit_country_id').select2('destroy');
            }
            if ($('#edit_administrative_unit_id').hasClass('select2-hidden-accessible')) {
                $('#edit_administrative_unit_id').select2('destroy');
            }
            if ($('#edit_author_id').hasClass('select2-hidden-accessible')) {
                $('#edit_author_id').select2('destroy');
            }

            $('#edit_role_id, #edit_level_id, #edit_country_id, #edit_administrative_unit_id, #edit_author_id').select2({
                width: '100%'
            });

            setTimeout(function() {
                $('#edit_level_id').val(levelId).trigger('change');
                var levelIsViewer = ($('#edit_level_id option:selected').attr('data-level-name') || '').toLowerCase() === 'viewer';
                viewerRoleVisibility($('#editUserModal .js-access-level-select'));
                if (!levelIsViewer) {
                    $('#edit_role_id').val(roleId).trigger('change');
                }
                $('#edit_country_id').val(countryId).trigger('change');
                $('#edit_administrative_unit_id').val(administrativeUnitId).trigger('change');
                $('#edit_author_id').val(authorId).trigger('change');
            }, 100);

            $('#editUserModal').modal('show');
        });

        $(document).on('click', '.btn-reset-user', function(){
            $('#reset_user_id').val($(this).data('id'));
            $('#resetUserModal').modal('show');
        });
        $(document).on('click', '.btn-delete-user', function(){
            $('#delete_user_id').val($(this).data('id'));
            $('#deleteUserModal').modal('show');
        });
        $(document).on('click', '.btn-send-verify', function(){
            $('#send_verify_user_id').val($(this).data('id'));
            document.getElementById('sendVerifyForm').submit();
        });
        $(document).on('click', '.btn-mark-verify', function(){
            $('#mark_verify_user_id').val($(this).data('id'));
            document.getElementById('markVerifyForm').submit();
        });
    });
</script>
@include('admin.permissions.partials.global_user_modals')
@endsection
