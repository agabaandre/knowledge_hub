<div class="tab-pane fade" id="homepage" role="tabpanel">
    <div class="form-section-title">
        <i class="fa fa-home"></i>
        Homepage Sections
    </div>
    <small class="info-text mb-3 d-block">Toggle which blocks appear on the homepage.</small>

    <div class="settings-toggle-grid">
        <div class="settings-toggle-card">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="show_featured" name="show_featured" value="1" @if(!empty($settings->show_featured)) checked @endif>
                <label class="form-check-label" for="show_featured">Featured content</label>
            </div>
        </div>
        <div class="settings-toggle-card">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="show_events" name="show_events" value="1" @if(!empty($settings->show_events)) checked @endif>
                <label class="form-check-label" for="show_events">Events slider</label>
            </div>
        </div>
        <div class="settings-toggle-card">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="show_top_searches" name="show_top_searches" value="1" @if(!empty($settings->show_top_searches)) checked @endif>
                <label class="form-check-label" for="show_top_searches">Top Searches</label>
            </div>
        </div>
        <div class="settings-toggle-card">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="show_tags" name="show_tags" value="1" @if(!empty($settings->show_tags)) checked @endif>
                <label class="form-check-label" for="show_tags">Tags strip</label>
            </div>
        </div>
        <div class="settings-toggle-card">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="show_quotes" name="show_quotes" value="1" @if(!empty($settings->show_quotes)) checked @endif>
                <label class="form-check-label" for="show_quotes">Quotes banner</label>
            </div>
        </div>
        <div class="settings-toggle-card">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="show_health_themes" name="show_health_themes" value="1" @if($settings->show_health_themes ?? true) checked @endif>
                <label class="form-check-label" for="show_health_themes">Health theme tiles</label>
            </div>
        </div>
        <div class="settings-toggle-card">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="show_quiz" name="show_quiz" value="1" @if(!empty($settings->show_quiz)) checked @endif>
                <label class="form-check-label" for="show_quiz">Quiz on search results</label>
            </div>
        </div>
    </div>

    <div class="form-section-title mt-4">
        <i class="fa fa-heading"></i>
        Section Titles
    </div>
    <small class="info-text mb-2 d-block">Leave blank to use the default placeholder text.</small>

    <div class="row settings-grid-row">
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label>Health themes section</label>
                <input type="text" name="section_title_health_themes" class="form-control" value="{{ $settings->section_title_health_themes ?? '' }}" placeholder="Choose a Health Theme to Explore">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label>Top Searches section</label>
                <input type="text" name="section_title_top_searches" class="form-control" value="{{ $settings->section_title_top_searches ?? '' }}" placeholder="Top Searches">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label>Recommended / Featured</label>
                <input type="text" name="section_title_recommended" class="form-control" value="{{ $settings->section_title_recommended ?? '' }}" placeholder="Recommended">
            </div>
        </div>
    </div>

    <div class="row settings-grid-row mt-2">
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label>Flagship Initiatives</label>
                <input type="text" name="section_title_flagship_initiatives" class="form-control" value="{{ $settings->section_title_flagship_initiatives ?? '' }}" placeholder="Flagship Initiatives">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label>Theme cards opacity</label>
                <select name="theme_card_opacity" class="form-control">
                    @foreach(['0.5' => '50%', '0.6' => '60%', '0.7' => '70%', '0.8' => '80%', '0.9' => '90%', '1' => '100%'] as $val => $label)
                        <option value="{{ $val }}" @if(($settings->theme_card_opacity ?? '1') == $val) selected @endif>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label>Theme cards per row (desktop)</label>
                <input type="number" name="theme_cards_per_row" class="form-control" min="2" max="8" value="{{ (int) ($settings->theme_cards_per_row ?? 4) }}">
            </div>
        </div>
    </div>
</div>
