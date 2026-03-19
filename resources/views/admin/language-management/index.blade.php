@extends(admin_layout())

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin_nav.language_management') }}</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.configure') }}">Settings</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('admin_nav.language_management') }}</li>
            </ol>
        </div>
    </div>

    @if (Session::has('alert-success') || Session::has('alert-danger'))
        <div class="alert alert-{{ Session::has('alert-success') ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
            {{ Session::get('alert-success') ?? Session::get('alert-danger') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('general.close') }}"></button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <p class="text-muted mb-0">
                Edit UI strings for the public site navigation, admin sidebar, and static footer/account chrome.
                English is the source of keys; other locales fall back to English until you save a value here.
                Existing Laravel groups such as <code>general</code> and <code>auth</code> are unchanged.
            </p>
        </div>
    </div>

    <form method="get" action="{{ route('admin.language-management.index') }}" class="row g-2 mb-3">
        <div class="col-md-4">
            <label class="form-label">{{ __('general.select') }} locale</label>
            <select name="locale" class="form-select" onchange="this.form.submit()">
                @foreach ($locales as $code)
                    <option value="{{ $code }}" @selected($code === $currentLocale)>
                        {{ $localeLabels[$code]['flag'] ?? '' }} {{ strtoupper($code) }}
                        — {{ $localeLabels[$code]['name'] ?? $code }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Section</label>
            <select name="group" class="form-select" onchange="this.form.submit()">
                @foreach ($groups as $key => $label)
                    <option value="{{ $key }}" @selected($key === $currentGroup)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </form>

    <form method="post" action="{{ route('admin.language-management.update') }}">
        @csrf
        <input type="hidden" name="locale" value="{{ $currentLocale }}">
        <input type="hidden" name="group" value="{{ $currentGroup }}">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ $groups[$currentGroup] ?? $currentGroup }}</span>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa fa-save me-1"></i>{{ __('general.save') }}
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center text-muted" style="width:3.5rem">#</th>
                                <th style="width:20%">Key</th>
                                <th style="width:38%">English (reference)</th>
                                <th style="width:38%">{{ strtoupper($currentLocale) }} translation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lines as $key => $value)
                                <tr>
                                    <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                                    <td><code class="small">{{ $key }}</code></td>
                                    <td class="small text-muted">{{ $english[$key] ?? '' }}</td>
                                    <td>
                                        <input type="text"
                                               name="translations[{{ $key }}]"
                                               value="{{ old('translations.'.$key, $value) }}"
                                               class="form-control form-control-sm"
                                               autocomplete="off">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save me-1"></i>{{ __('general.save') }}
                </button>
            </div>
        </div>
    </form>
@endsection
