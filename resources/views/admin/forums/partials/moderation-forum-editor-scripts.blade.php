<script src="{{ asset('assets/plugins/summernote/dist/summernote.min.js') }}"></script>
<script>
(function () {
    var grammarUrl = @json(route('admin.forums.moderation.grammar-assist'));
    var saveUrl = @json(route('admin.forums.moderation.update-pending'));
    var csrf = @json(csrf_token());

    function initForumModEditor($modal) {
        $modal.find('textarea.forum-mod-editor').each(function () {
            var $ta = $(this);
            if ($ta.data('forumModSummernote')) {
                return;
            }
            var initialHtml = $ta.val() || '';
            $ta.summernote({
                placeholder: 'Post body',
                tabsize: 2,
                height: 280,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview']]
                ]
            });
            if (initialHtml) {
                $ta.summernote('code', initialHtml);
            }
            $ta.data('forumModSummernote', true);
        });
    }

    window.forumAdminInitPendingEditor = initForumModEditor;

    $(document).on('shown.bs.modal', '.modal[id^="details"]', function () {
        initForumModEditor($(this));
    });

    function setStatus(forumId, msg, isError) {
        var $s = $('#mod_status_' + forumId);
        if (!$s.length) return;
        $s.text(msg).css('color', isError ? '#c0392b' : '#119A48').show();
        if (!isError && msg) {
            setTimeout(function () { $s.fadeOut(); }, 4000);
        }
    }

    $(document).on('click', '.btn-forum-ai-grammar', function () {
        var forumId = $(this).data('forum-id');
        var $modal = $('#details' + forumId);
        var $ta = $modal.find('#mod_body_' + forumId);
        if (!$ta.length || !$ta.data('forumModSummernote')) {
            alert('Open the editor first (click Review, or load this page for a pending forum).');
            return;
        }
        var html = $ta.summernote('code');
        if (!html || !String(html).trim()) {
            alert('Nothing to proofread.');
            return;
        }
        var $btn = $(this);
        $btn.prop('disabled', true);
        setStatus(forumId, 'AI is proofreading…', false);
        $.ajax({
            url: grammarUrl,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            data: { _token: csrf, html: html },
            success: function (res) {
                if (res && res.ok && res.html) {
                    $ta.summernote('code', res.html);
                    setStatus(forumId, 'Grammar suggestions applied. Review before saving.', false);
                } else {
                    setStatus(forumId, (res && res.error) ? res.error : 'AI request failed.', true);
                }
            },
            error: function (xhr) {
                var err = 'Request failed.';
                try {
                    var j = xhr.responseJSON;
                    if (j && j.error) err = j.error;
                    else if (j && j.message) err = j.message;
                } catch (e) {}
                setStatus(forumId, err, true);
            },
            complete: function () {
                $btn.prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.btn-forum-save-pending', function () {
        var forumId = $(this).data('forum-id');
        var $modal = $('#details' + forumId);
        var title = ($modal.find('#mod_title_' + forumId).val() || '').trim();
        var $ta = $modal.find('#mod_body_' + forumId);
        if (!$ta.length || !$ta.data('forumModSummernote')) {
            alert('Editor not ready.');
            return;
        }
        var body = $ta.summernote('code');
        if (!title) {
            alert('Title is required.');
            return;
        }
        var $btn = $(this);
        $btn.prop('disabled', true);
        setStatus(forumId, 'Saving…', false);
        $.ajax({
            url: saveUrl,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            data: {
                _token: csrf,
                id: forumId,
                forum_title: title,
                forum_description: body
            },
            success: function () {
                setStatus(forumId, 'Saved. Reloading…', false);
                window.location.reload();
            },
            error: function (xhr) {
                var err = 'Save failed.';
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    err = Object.values(xhr.responseJSON.errors).flat().join(' ');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    err = xhr.responseJSON.message;
                }
                setStatus(forumId, err, true);
                $btn.prop('disabled', false);
            }
        });
    });
})();
</script>
