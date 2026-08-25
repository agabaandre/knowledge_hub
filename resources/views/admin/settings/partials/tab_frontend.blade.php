<div class="tab-pane fade" id="frontend" role="tabpanel">
    <div class="form-section-title">
        <i class="fa fa-desktop"></i>
        Next.js public frontend
    </div>
    <p class="info-text mb-3">
        Controls the template at
        <a href="{{ url('front_end') }}" target="_blank" rel="noopener">{{ url('front_end') }}</a>.
        Built-in designs: University, Language Academy, and Online Course. Uploaded packs reuse one of those bases (molecule / nucleus) and may add CSS tokens.
    </p>

    <div class="form-group">
        <label>Default frontend template</label>
        <select class="form-control" name="frontend_theme">
            @foreach(($frontendThemes ?? []) as $theme)
                <option value="{{ $theme['id'] }}" @if(($activeFrontendTheme['id'] ?? 'university') === $theme['id']) selected @endif>
                    {{ $theme['name'] }} (built-in)
                </option>
            @endforeach
            @foreach(($frontendThemePacks ?? []) as $pack)
                <option value="{{ $pack['id'] }}" @if(($activeFrontendTheme['id'] ?? '') === $pack['id']) selected @endif>
                    {{ $pack['name'] }} (uploaded · extends {{ $pack['extends'] }})
                </option>
            @endforeach
        </select>
        <small class="info-text">Saved with the main Configure form. The Next.js shell reads this via <code>/api/lookup/frontend-theme</code>.</small>
    </div>

    <div class="form-section-title mt-4">
        <i class="fa fa-upload"></i>
        Upload a theme pack
    </div>
    <p class="info-text">ZIP must include <code>theme.json</code> (<code>name</code>, <code>extends</code>: university, language-academy, or online-course) and may include <code>tokens.css</code>. Copy <code>front_end/src/themes/_starter/</code> to start a new pack.</p>
    <div class="form-group">
        <label>Theme pack (.zip)</label>
        <input type="file" name="frontend_theme_pack" accept=".zip,application/zip" class="form-control" form="frontend-theme-upload-form">
    </div>
    <div class="form-group">
        <label class="mb-0">
            <input type="checkbox" name="activate_frontend_theme" value="1" form="frontend-theme-upload-form">
            Activate this pack after upload
        </label>
    </div>
    <button type="submit" class="btn btn-outline-primary" form="frontend-theme-upload-form">
        Upload theme pack
    </button>

    @if(!empty($frontendThemePacks))
        <div class="form-section-title mt-4">
            <i class="fa fa-archive"></i>
            Uploaded packs
        </div>
        <ul class="list-unstyled">
            @foreach($frontendThemePacks as $pack)
                <li class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                    <span>
                        <strong>{{ $pack['name'] }}</strong>
                        <small class="text-muted"> · {{ $pack['id'] }} · extends {{ $pack['extends'] }}</small>
                    </span>
                    <button type="submit" form="frontend-theme-delete-{{ $pack['id'] }}" class="btn btn-sm btn-outline-danger">Remove</button>
                </li>
            @endforeach
        </ul>
    @endif
</div>
