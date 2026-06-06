@php
    $healthTopicSources = $healthTopicSources ?? \App\Support\HealthTopicSourceCatalog::sources();
@endphp

<div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header bg-white py-3">
        <h5 class="mb-1"><i class="fa fa-magic text-success mr-2"></i>AI Health Topics</h5>
        <p class="text-muted small mb-0">
            Generate unique diseases and conditions with <strong>rich HTML descriptions</strong> (headings, lists, references)
            using WHO factsheets plus reference lists from CDC, MedlinePlus, UC Berkeley UHS, and Dartmouth.
            Duplicates are filtered before and after AI generation. Runs only when you click the buttons below.
            Default: Health Topic <strong>Yes</strong>, Health Emergency <strong>No</strong>.
            @if(($missingOverviewCount ?? 0) > 0)
                <span class="d-block mt-1"><strong>{{ $missingOverviewCount }}</strong> existing tag(s) are missing or have very short descriptions.</span>
            @endif
        </p>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-8">
                <label class="font-weight-bold small text-uppercase text-muted">Reference sources</label>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @foreach($healthTopicSources as $key => $source)
                        <label class="btn btn-sm btn-outline-secondary mb-1 mr-1 mb-2">
                            <input type="checkbox" class="js-ai-source mr-1" name="ai_sources[]" value="{{ $key }}" checked>
                            {{ $source['label'] }}
                        </label>
                    @endforeach
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="aiTopicCount">Topics to generate</label>
                        <input type="number" id="aiTopicCount" class="form-control" min="5" max="40" value="15">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="aiHealthTopic">Health Topic</label>
                        <select id="aiHealthTopic" class="form-control">
                            <option value="1" selected>Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="aiHealthEmergency">Health Emergency</label>
                        <select id="aiHealthEmergency" class="form-control">
                            <option value="0" selected>No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 d-flex flex-column justify-content-center">
                <button type="button" class="btn btn-success mb-2" id="btnAiGenerateTopics">
                    <i class="fa fa-magic mr-1"></i> Generate with AI
                </button>
                <button type="button" class="btn btn-outline-primary mb-2" id="btnFillMissingDescriptions">
                    <i class="fa fa-file-medical mr-1"></i> Fill missing descriptions (WHO)
                </button>
                <button type="button" class="btn btn-outline-primary mb-2" id="btnDeduplicateTags">
                    <i class="fa fa-compress-arrows-alt mr-1"></i> Deduplicate tags
                </button>
                <small class="text-muted">WHO descriptions use factsheet content with HTML formatting and reference links. Updates apply only when the new text is longer (unless you force on an individual row).</small>
            </div>
        </div>

        <div id="aiTagsStatus" class="alert d-none mt-3 mb-0" role="alert"></div>
    </div>
</div>

<div class="modal fade" id="aiTopicsPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review generated health topics</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted small" id="aiTopicsPreviewMeta"></p>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead>
                        <tr>
                            <th style="width:40px;"><input type="checkbox" id="aiTopicsSelectAll" checked></th>
                            <th style="width:28%;">Tag</th>
                            <th>Description preview (HTML)</th>
                            <th style="width:90px;">Status</th>
                        </tr>
                        </thead>
                        <tbody id="aiTopicsPreviewBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnAiImportTopics">
                    <i class="fa fa-download mr-1"></i> Import selected
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="describePreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review WHO-based descriptions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="describePreviewBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnApplyDescriptions">
                    <i class="fa fa-save mr-1"></i> Apply selected updates
                </button>
            </div>
        </div>
    </div>
</div>

@push('modal-scripts')
<script>
(function () {
    const csrf = @json(csrf_token());
    const generateUrl = @json(route('tags.ai-generate'));
    const importUrl = @json(route('tags.ai-import'));
    const describeUrl = @json(route('tags.ai-describe'));
    const applyOverviewsUrl = @json(route('tags.ai-apply-overviews'));
    const dedupeUrl = @json(route('tags.deduplicate'));
    let generatedTopics = [];
    let describeItems = [];

    function showStatus(type, message) {
        const $box = $('#aiTagsStatus');
        $box.removeClass('d-none alert-success alert-danger alert-info')
            .addClass('alert-' + type)
            .text(message);
    }

    function selectedSources() {
        const sources = [];
        $('.js-ai-source:checked').each(function () {
            sources.push(this.value);
        });
        return sources;
    }

    function stripHtml(html) {
        const div = document.createElement('div');
        div.innerHTML = html || '';
        return (div.textContent || div.innerText || '').trim();
    }

    function renderPreview(topics, meta) {
        generatedTopics = topics || [];
        const $body = $('#aiTopicsPreviewBody').empty();
        topics.forEach(function (topic, idx) {
            const dup = !!topic.is_duplicate;
            const preview = stripHtml(topic.overview).substring(0, 220);
            $body.append(
                '<tr data-idx="' + idx + '"' + (dup ? ' class="table-warning"' : '') + '>' +
                '<td><input type="checkbox" class="js-ai-topic-select"' + (dup ? '' : ' checked') + (dup ? ' disabled' : '') + '></td>' +
                '<td>' + $('<div>').text(topic.tag_text).html() + '</td>' +
                '<td class="small"><div class="text-muted mb-1">' + $('<div>').text(preview + (stripHtml(topic.overview).length > 220 ? '…' : '')).html() + '</div>' +
                '<details><summary class="small">View HTML</summary><div class="border rounded p-2 mt-1" style="max-height:180px;overflow:auto;">' + (topic.overview || '') + '</div></details></td>' +
                '<td>' + (dup ? '<span class="badge badge-warning">Duplicate</span>' : '<span class="badge badge-success">New</span>') + '</td>' +
                '</tr>'
            );
        });
        $('#aiTopicsPreviewMeta').text(meta || '');
        $('#aiTopicsPreviewModal').modal('show');
    }

    $('#aiTopicsSelectAll').on('change', function () {
        const checked = this.checked;
        $('.js-ai-topic-select:not(:disabled)').prop('checked', checked);
    });

    $('#btnAiGenerateTopics').on('click', function () {
        const $btn = $(this).prop('disabled', true);
        showStatus('info', 'Fetching reference topics and generating with AI…');

        $.ajax({
            url: generateUrl,
            method: 'POST',
            data: {
                _token: csrf,
                count: $('#aiTopicCount').val(),
                sources: selectedSources()
            }
        }).done(function (res) {
            showStatus('success', res.message || 'Generation complete.');
            const meta = 'Reference topics collected: ' + (res.reference_topic_count || 0) +
                '. Sources: ' + ((res.sources_used || []).join(', ') || '—');
            renderPreview(res.topics || [], meta);
        }).fail(function (xhr) {
            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'AI generation failed.';
            showStatus('danger', msg);
        }).always(function () {
            $btn.prop('disabled', false);
        });
    });

    $('#btnAiImportTopics').on('click', function () {
        const selected = [];
        $('#aiTopicsPreviewBody tr').each(function () {
            const idx = parseInt($(this).data('idx'), 10);
            if ($(this).find('.js-ai-topic-select').is(':checked') && generatedTopics[idx]) {
                selected.push(generatedTopics[idx]);
            }
        });
        if (!selected.length) {
            alert('Select at least one new topic to import.');
            return;
        }

        const $btn = $(this).prop('disabled', true);
        $.ajax({
            url: importUrl,
            method: 'POST',
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },
            data: JSON.stringify({
                topics: selected,
                is_health_topic: $('#aiHealthTopic').val(),
                is_health_emergency: $('#aiHealthEmergency').val()
            })
        }).done(function (res) {
            $('#aiTopicsPreviewModal').modal('hide');
            showStatus('success', res.message || 'Imported.');
            setTimeout(function () { window.location.reload(); }, 800);
        }).fail(function (xhr) {
            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Import failed.';
            alert(msg);
        }).always(function () {
            $btn.prop('disabled', false);
        });
    });

    function renderDescribePreview(items) {
        describeItems = items || [];
        const $body = $('#describePreviewBody').empty();
        if (!describeItems.length) {
            $body.html('<p class="text-muted mb-0">No descriptions generated.</p>');
            $('#describePreviewModal').modal('show');
            return;
        }

        describeItems.forEach(function (item, idx) {
            if (!item.ok) {
                $body.append(
                    '<div class="alert alert-warning">' +
                    $('<div>').text(item.tag_text + ': ' + (item.error || 'Failed')).html() +
                    '</div>'
                );
                return;
            }

            const refs = (item.references || []).map(function (r) {
                return '<li><a href="' + r.url + '" target="_blank" rel="noopener">' + $('<div>').text(r.label).html() + '</a></li>';
            }).join('');

            $body.append(
                '<div class="card mb-3 js-describe-card" data-idx="' + idx + '">' +
                '<div class="card-header d-flex justify-content-between align-items-center">' +
                '<div><input type="checkbox" class="js-describe-select mr-2" checked> <strong>' + $('<div>').text(item.tag_text).html() + '</strong></div>' +
                '<span class="badge badge-light">' + (item.new_length || 0) + ' chars</span>' +
                '</div>' +
                '<div class="card-body">' +
                (item.existing_length ? '<p class="small text-muted">Previous: ' + item.existing_length + ' chars. Update applies only if longer.</p>' : '') +
                '<div class="border rounded p-3 mb-2" style="max-height:320px;overflow:auto;">' + (item.overview || '') + '</div>' +
                (refs ? '<p class="small mb-0"><strong>Sources</strong><ul class="small mb-0">' + refs + '</ul></p>' : '') +
                '</div></div>'
            );
        });

        $('#describePreviewModal').modal('show');
    }

    function requestDescriptions(payload, statusMessage) {
        showStatus('info', statusMessage || 'Fetching WHO factsheets and generating HTML descriptions…');
        return $.ajax({
            url: describeUrl,
            method: 'POST',
            data: Object.assign({ _token: csrf }, payload || {})
        });
    }

    $('#btnFillMissingDescriptions').on('click', function () {
        const $btn = $(this).prop('disabled', true);
        requestDescriptions({ limit: 5, only_missing: 1 })
            .done(function (res) {
                showStatus('success', res.message || 'Descriptions generated.');
                renderDescribePreview(res.items || []);
            })
            .fail(function (xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Description generation failed.';
                showStatus('danger', msg);
                if (xhr.responseJSON && xhr.responseJSON.items) {
                    renderDescribePreview(xhr.responseJSON.items);
                }
            })
            .always(function () { $btn.prop('disabled', false); });
    });

    $(document).on('click', '.js-describe-tag', function () {
        const tagId = $(this).data('tag-id');
        const tagText = $(this).data('tag-text');
        const $btn = $(this).prop('disabled', true);
        requestDescriptions({ tag_id: tagId, only_missing: 0 })
            .done(function (res) {
                showStatus('success', res.message || ('Generated description for ' + tagText));
                renderDescribePreview(res.items || []);
            })
            .fail(function (xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Description generation failed.';
                alert(msg);
            })
            .always(function () { $btn.prop('disabled', false); });
    });

    $('#btnApplyDescriptions').on('click', function () {
        const updates = [];
        $('.js-describe-card').each(function () {
            const idx = parseInt($(this).data('idx'), 10);
            if (!$(this).find('.js-describe-select').is(':checked')) {
                return;
            }
            const item = describeItems[idx];
            if (item && item.ok && item.overview) {
                updates.push({ tag_id: item.tag_id, overview: item.overview });
            }
        });

        if (!updates.length) {
            alert('Select at least one description to apply.');
            return;
        }

        const $btn = $(this).prop('disabled', true);
        $.ajax({
            url: applyOverviewsUrl,
            method: 'POST',
            contentType: 'application/json',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            data: JSON.stringify({ updates: updates, only_if_longer: true })
        }).done(function (res) {
            $('#describePreviewModal').modal('hide');
            showStatus('success', res.message || 'Descriptions updated.');
            setTimeout(function () { window.location.reload(); }, 900);
        }).fail(function (xhr) {
            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Could not apply descriptions.';
            alert(msg);
        }).always(function () { $btn.prop('disabled', false); });
    });

    $('#btnDeduplicateTags').on('click', function () {
        if (!confirm('Merge duplicate tags (same name, different capitalization/spacing)? Content will be remapped to the oldest tag in each group.')) {
            return;
        }
        const $btn = $(this).prop('disabled', true);
        showStatus('info', 'Deduplicating tags…');
        $.ajax({
            url: dedupeUrl,
            method: 'POST',
            data: { _token: csrf }
        }).done(function (res) {
            showStatus('success', res.message || 'Done.');
            if ((res.merged_tags || 0) > 0) {
                setTimeout(function () { window.location.reload(); }, 900);
            }
        }).fail(function (xhr) {
            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Deduplication failed.';
            showStatus('danger', msg);
        }).always(function () {
            $btn.prop('disabled', false);
        });
    });
})();
</script>
@endpush
