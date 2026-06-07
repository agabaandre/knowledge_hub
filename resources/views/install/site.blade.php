@extends('install.layout')

@section('title', 'Site settings')

@section('content')
    <div class="step-badge text-muted mb-2">Step 4 of 8</div>
    <h2 class="h5 mb-3">Site settings</h2>
    <p class="text-muted small">These values are saved to the active <strong>setting</strong> record so the hub can render the homepage, SEO metadata, and contact details.</p>

    <form method="post" action="{{ route('install.site.store') }}" class="row g-3">
        @csrf
        <div class="col-md-6">
            <label class="form-label">Site name <span class="text-danger">*</span></label>
            <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $defaults['site_name']) }}" required maxlength="100">
        </div>
        <div class="col-md-6">
            <label class="form-label">Page title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $defaults['title']) }}" maxlength="255" placeholder="Same as site name if empty">
        </div>
        <div class="col-12">
            <label class="form-label">Tagline / slogan</label>
            <input type="text" name="slogan" class="form-control" value="{{ old('slogan', $defaults['slogan']) }}" maxlength="255">
        </div>
        <div class="col-12">
            <label class="form-label">Site description <span class="text-danger">*</span></label>
            <textarea name="site_description" class="form-control" rows="3" required maxlength="5000">{{ old('site_description', $defaults['site_description']) }}</textarea>
        </div>
        <div class="col-12">
            <label class="form-label">SEO keywords</label>
            <input type="text" name="seo_keywords" class="form-control" value="{{ old('seo_keywords', $defaults['seo_keywords']) }}" placeholder="Comma-separated keywords">
        </div>
        <div class="col-md-6">
            <label class="form-label">Contact email <span class="text-danger">*</span></label>
            <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $defaults['contact_email']) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Contact phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $defaults['phone']) }}" maxlength="20">
        </div>
        <div class="col-12">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="{{ old('address', $defaults['address']) }}" maxlength="500">
        </div>
        <div class="col-md-6">
            <label class="form-label">Timezone <span class="text-danger">*</span></label>
            <select name="timezone" class="form-select" required>
                @foreach ($timezones as $tz)
                    <option value="{{ $tz }}" @selected(old('timezone', $defaults['timezone']) === $tz)>{{ $tz }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 d-flex gap-2">
            <a href="{{ route('install.storage') }}" class="btn btn-outline-secondary">Back</a>
            <button type="submit" class="btn btn-success">Save site settings &amp; continue</button>
        </div>
    </form>
@endsection
