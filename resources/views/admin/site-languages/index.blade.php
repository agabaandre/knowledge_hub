@extends(admin_layout())

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin_nav.site_languages') }}</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.configure') }}">Settings</a></li>
                <li class="breadcrumb-item active">{{ __('admin_nav.site_languages') }}</li>
            </ol>
        </div>
    </div>

    @if (Session::has('alert-success') || Session::has('alert-danger'))
        <div class="alert alert-{{ Session::has('alert-success') ? 'success' : 'danger' }} alert-dismissible fade show">
            {{ Session::get('alert-success') ?? Session::get('alert-danger') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header fw-bold">{{ __('general.add') }} {{ __('admin_nav.site_languages') }}</div>
        <div class="card-body">
            <p class="text-muted small">
                <strong>Locale code</strong> is stored in user profiles and Laravel language files (e.g. <code>de</code>, <code>zh-cn</code>).
                <strong>Google Translate code</strong> must match what the Google widget supports (often the same; use values like <code>zh-CN</code> when they differ).
            </p>
            <form method="post" action="{{ route('admin.site-languages.store') }}" class="row g-3">
                @csrf
                <div class="col-md-2">
                    <label class="form-label">Locale code <span class="text-danger">*</span></label>
                    <input type="text" name="locale_code" class="form-control @error('locale_code') is-invalid @enderror"
                           value="{{ old('locale_code') }}" placeholder="de" maxlength="32" required pattern="[a-z]{2}([_-][a-zA-Z0-9]+)*">
                    @error('locale_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Display name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required maxlength="120">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label">Google Translate code</label>
                    <input type="text" name="google_translate_code" class="form-control @error('google_translate_code') is-invalid @enderror"
                           value="{{ old('google_translate_code') }}" placeholder="same as locale if empty" maxlength="32">
                    @error('google_translate_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-1">
                    <label class="form-label">Flag</label>
                    <input type="text" name="flag_emoji" class="form-control" value="{{ old('flag_emoji') }}" placeholder="🇩🇪" maxlength="16">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Sort</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 100) }}" min="0">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="add_active" checked>
                        <label class="form-check-label" for="add_active">Active</label>
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">{{ __('general.save') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header fw-bold">Configured languages</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center text-muted" style="width:3.5rem">#</th>
                            <th>Locale</th>
                            <th>Name</th>
                            <th>Google code</th>
                            <th>Flag</th>
                            <th>Sort</th>
                            <th>Active</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($languages as $lang)
                            <tr>
                                <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                <td><code>{{ $lang->locale_code }}</code></td>
                                <td>{{ $lang->name }}</td>
                                <td><code>{{ $lang->google_translate_code ?: '—' }}</code></td>
                                <td>{{ $lang->flag_emoji }}</td>
                                <td>{{ $lang->sort_order }}</td>
                                <td>{{ $lang->is_active ? 'Yes' : 'No' }}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="modal" data-bs-target="#editLangModal"
                                            data-url="{{ route('admin.site-languages.update', $lang) }}"
                                            data-name="{{ e($lang->name) }}"
                                            data-google="{{ e($lang->google_translate_code ?? '') }}"
                                            data-flag="{{ e($lang->flag_emoji ?? '') }}"
                                            data-sort="{{ $lang->sort_order }}"
                                            data-active="{{ $lang->is_active ? '1' : '0' }}"
                                            data-locale="{{ e($lang->locale_code) }}">
                                        {{ __('general.edit') }}
                                    </button>
                                    @if (strtolower($lang->locale_code) !== 'en')
                                        <form method="post" action="{{ route('admin.site-languages.destroy', $lang) }}" class="d-inline"
                                              onsubmit="return confirm('Delete this language?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('general.delete') }}</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editLangModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" id="editLangForm" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit language <code id="editLocaleLabel"></code></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Display name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required maxlength="120">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Google Translate code</label>
                        <input type="text" name="google_translate_code" id="edit_google" class="form-control" maxlength="32" placeholder="Leave empty to use locale code">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Flag emoji</label>
                        <input type="text" name="flag_emoji" id="edit_flag" class="form-control" maxlength="16">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort order</label>
                        <input type="number" name="sort_order" id="edit_sort" class="form-control" min="0">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_active">
                        <label class="form-check-label" for="edit_active">Active (shown in nav & profile)</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('general.close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('general.update') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
(function() {
    var modal = document.getElementById('editLangModal');
    if (!modal) return;
    modal.addEventListener('show.bs.modal', function(ev) {
        var btn = ev.relatedTarget;
        if (!btn) return;
        document.getElementById('editLangForm').action = btn.getAttribute('data-url');
        document.getElementById('editLocaleLabel').textContent = btn.getAttribute('data-locale') || '';
        document.getElementById('edit_name').value = btn.getAttribute('data-name') || '';
        document.getElementById('edit_google').value = btn.getAttribute('data-google') || '';
        document.getElementById('edit_flag').value = btn.getAttribute('data-flag') || '';
        document.getElementById('edit_sort').value = btn.getAttribute('data-sort') || '0';
        document.getElementById('edit_active').checked = btn.getAttribute('data-active') === '1';
    });
})();
</script>
@endsection
