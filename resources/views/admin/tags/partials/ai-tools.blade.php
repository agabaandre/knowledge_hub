@php
    $healthTopicSources = $healthTopicSources ?? \App\Support\HealthTopicSourceCatalog::sources();
@endphp

<div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header bg-white py-3">
        <h5 class="mb-1"><i class="fa fa-magic text-success mr-2"></i>AI Health Topics</h5>
        <p class="text-muted small mb-0">
            Generate unique diseases and conditions with descriptions using reference lists from
            WHO, MedlinePlus, UC Berkeley UHS, and Dartmouth. Runs only when you click the buttons below.
            Default: Health Topic <strong>Yes</strong>, Health Emergency <strong>No</strong>.
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
                <button type="button" class="btn btn-outline-primary mb-2" id="btnDeduplicateTags">
                    <i class="fa fa-compress-arrows-alt mr-1"></i> Deduplicate tags
                </button>
                <small class="text-muted">Deduplication merges exact name duplicates (case-insensitive) and remaps publications, forums, and communities.</small>
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
                            <th>Description preview</th>
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

@push('modal-scripts')
<script>
(function () {
    const csrf = @json(csrf_token());
    const generateUrl = @json(route('tags.ai-generate'));
    const importUrl = @json(route('tags.ai-import'));
    const dedupeUrl = @json(route('tags.deduplicate'));
    let generatedTopics = [];

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
            const preview = stripHtml(topic.overview).substring(0, 180);
            $body.append(
                '<tr data-idx="' + idx + '"' + (dup ? ' class="table-warning"' : '') + '>' +
                '<td><input type="checkbox" class="js-ai-topic-select"' + (dup ? '' : ' checked') + (dup ? ' disabled' : '') + '></td>' +
                '<td>' + $('<div>').text(topic.tag_text).html() + '</td>' +
                '<td class="small text-muted">' + $('<div>').text(preview + (preview.length >= 180 ? '…' : '')).html() + '</td>' +
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
