<div class="pub-filters-grid pub-filters-grid--primary">
    <div class="pub-filter-field">
        <label class="pub-filter-label" for="filterTitle">{{ ui_t('admin_publications.filter_keyword', 'Keyword') }}</label>
        <input type="text" name="term" id="filterTitle" class="form-control pub-filter-input" placeholder="{{ ui_t('admin_publications.filter_keyword_placeholder', 'Search by title or keyword') }}" value="{{ @$search->term ?? '' }}">
    </div>
    <div class="pub-filter-field">
        <label class="pub-filter-label" for="author">{{ ui_t('admin_publications.filter_source_author', 'Source / Author') }}</label>
        @include('partials.authors.dropdown', [
            'field' => 'author',
            'selected' => @$search->author,
            'class' => 'pub-filter-input select2 form-control',
        ])
    </div>
    <div class="pub-filter-field">
        <label class="pub-filter-label" for="file_type">{{ ui_t('admin_publications.filter_file_type', 'File Type') }}</label>
        @include('partials.publications.filetype_dropdown', [
            'field' => 'file_type',
            'selected' => @$search->file_type,
            'class' => 'pub-filter-input select2 form-control',
        ])
    </div>
</div>
