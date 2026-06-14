<script>
@php
    $communityWallPostUrl = community_detail_url($community, true, ['tab' => 'wall', 'post' => 1]);
@endphp
(function () {
    function fallbackCopy(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); } catch (e) {}
        document.body.removeChild(ta);
    }

    window.toggleCommunityCommentForm = function () {
        var container = document.getElementById('communityCommentFormContainer');
        var icon = document.getElementById('communityCommentFormToggleIcon');
        if (!container || !icon) return;
        var open = container.style.display === 'none' || container.style.display === '';
        container.style.display = open ? 'block' : 'none';
        icon.style.transform = open ? 'rotate(180deg)' : 'rotate(0deg)';
        if (open) {
            setTimeout(function () {
                var ta = document.getElementById('communityCommentTextarea');
                if (ta) ta.focus();
            }, 100);
        }
    };

    window.openCommunityWallPost = function () {
        function openWallPostUi() {
            var container = document.getElementById('communityCommentFormContainer');
            if (!container || container.style.display === 'none' || container.style.display === '') {
                window.toggleCommunityCommentForm();
            }
            var card = document.getElementById('communityWallPostFormCard');
            if (card && card.scrollIntoView) {
                card.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            var ta = document.getElementById('communityCommentTextarea');
            if (ta) ta.focus();
        }

        var wallTab = document.getElementById('wall-posts');
        if (wallTab && !wallTab.classList.contains('active')) {
            if (typeof window.communityDetailSwitchTab === 'function') {
                window.communityDetailSwitchTab('wall', { keepPost: true });
                setTimeout(openWallPostUi, 100);
                return;
            }
            window.location.href = @json($communityWallPostUrl);
            return;
        }
        openWallPostUi();
    };

    function updateCharCount(textarea) {
        var maxWords = 300;
        var text = (textarea.value || '').trim();
        var words = text.split(/\s+/).filter(function (w) { return w.length > 0; });
        var span = textarea.parentElement && textarea.parentElement.querySelector('.comment-char-count .char-count');
        if (span) span.textContent = words.length;
        if (words.length > maxWords) {
            textarea.value = words.slice(0, maxWords).join(' ');
            updateCharCount(textarea);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.comment-textarea').forEach(function (ta) {
            ta.addEventListener('input', function () { updateCharCount(ta); });
            updateCharCount(ta);
        });
    });

    if (typeof jQuery !== 'undefined') {
        jQuery(document).off('click', '.like-community-comment-btn').on('click', '.like-community-comment-btn', function (e) {
            e.preventDefault();
            var $btn = jQuery(this);
            jQuery.ajax({
                url: '{{ route('community.comment-like') }}',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                contentType: 'application/json',
                data: JSON.stringify({ comment_id: $btn.data('comment-id') }),
                success: function (data) {
                    if (data.error) { alert(data.error); return; }
                    var $icon = $btn.find('i');
                    $icon.removeClass('fa-heart fa-heart-o').addClass(data.liked ? 'fa-heart' : 'fa-heart-o');
                    $icon.css('color', data.liked ? '#ef4444' : 'inherit');
                    $btn.css('color', data.liked ? '#ef4444' : '#64748b');
                    $btn.find('.like-count').text(data.count + ' ' + (data.count === 1 ? 'like' : 'likes'));
                }
            });
        });

        jQuery(document).off('click', '.reply-comment-btn').on('click', '.reply-comment-btn', function (e) {
            e.preventDefault();
            var id = jQuery(this).data('comment-id');
            var $form = jQuery('#reply-form-' + id);
            if ($form.length) {
                $form.toggle();
                if ($form.is(':visible')) $form.find('textarea').focus();
            }
        });

        jQuery(document).off('click', '.cancel-reply-btn').on('click', '.cancel-reply-btn', function (e) {
            e.preventDefault();
            var id = jQuery(this).data('comment-id');
            jQuery('#reply-form-' + id).hide().find('textarea').val('');
        });

        jQuery(document).off('click', '.share-comment-btn').on('click', '.share-comment-btn', function (e) {
            e.preventDefault();
            var url = jQuery(this).data('share-url');
            if (!url) return;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url);
            } else {
                fallbackCopy(url);
            }
        });

        jQuery('#communityCommentForm').on('submit', function (e) {
            e.preventDefault();
            var $form = jQuery(this);
            var $btn = $form.find('button[type="submit"]');
            var original = $btn.html();
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Posting...');
            var formData = new FormData(this);
            jQuery.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function (response) {
                    if (response.success && response.comment_html) {
                        jQuery('#communityNoComments').hide();
                        var $list = jQuery('#communityCommentsList');
                        if (!$list.length) {
                            jQuery('.comments-section').append('<div id="communityCommentsList"></div>');
                            $list = jQuery('#communityCommentsList');
                        }
                        var $temp = jQuery('<div>').html(response.comment_html);
                        $temp.find('.comment-item').first().prependTo($list);
                        $form[0].reset();
                        jQuery('#communityFilePreview').empty();
                        toggleCommunityCommentForm();
                        var count = parseInt(jQuery('#communityCommentsCountLabel').text(), 10) || 0;
                        count += 1;
                        jQuery('#communityCommentsCountLabel').text(count);
                    } else if (response.success) {
                        window.location.reload();
                    } else {
                        alert(response.error || 'Failed to post comment.');
                    }
                },
                error: function (xhr) {
                    var msg = 'Failed to post comment.';
                    if (xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;
                    alert(msg);
                },
                complete: function () {
                    $btn.prop('disabled', false).html(original);
                }
            });
        });

        var uploadArea = document.getElementById('communityFileUploadArea');
        var fileInput = document.getElementById('communityCommentFiles');
        if (uploadArea && fileInput) {
            uploadArea.addEventListener('click', function () { fileInput.click(); });
            fileInput.addEventListener('change', function () {
                var preview = document.getElementById('communityFilePreview');
                if (!preview) return;
                preview.innerHTML = '';
                Array.prototype.forEach.call(fileInput.files, function (file) {
                    var chip = document.createElement('span');
                    chip.className = 'badge badge-light mr-1 mb-1';
                    chip.textContent = file.name;
                    preview.appendChild(chip);
                });
            });
        }
    }
})();
</script>
