<script>
(function () {
    function notifyCopy(message) {
        if (typeof window.notifyx === 'function') {
            window.notifyx('success', message);
        } else if (typeof Lobibox !== 'undefined') {
            Lobibox.notify('success', { size: 'mini', msg: message, delay: 2500, title: false });
        }
    }

    function copyShareLink(url) {
        if (!url) return;
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(url).then(function () {
                notifyCopy('Link copied to clipboard!');
            }).catch(function () {
                fallbackCopy(url);
            });
        } else {
            fallbackCopy(url);
        }
    }

    function fallbackCopy(url) {
        var el = document.createElement('textarea');
        el.value = url;
        el.style.position = 'fixed';
        el.style.left = '-9999px';
        document.body.appendChild(el);
        el.select();
        try {
            document.execCommand('copy');
            notifyCopy('Link copied to clipboard!');
        } catch (e) {
            window.prompt('Copy this link:', url);
        } finally {
            document.body.removeChild(el);
        }
    }

    window.togglePublicationComments = function (publicationId) {
        var list = document.getElementById('pub-comments-list-' + publicationId);
        var toggle = document.querySelector('.pub-comments-toggle[data-publication-id="' + publicationId + '"]');
        if (!list) return;
        var isHidden = list.style.display === 'none' || !list.style.display;
        list.style.display = isHidden ? 'block' : 'none';
        if (toggle) toggle.classList.toggle('collapsed', !isHidden);
    };

    window.showInlinePublicationCommentForm = function (publicationId) {
        var form = document.getElementById('pub-comment-form-' + publicationId);
        var btn = document.getElementById('pub-show-comment-btn-' + publicationId);
        var list = document.getElementById('pub-comments-list-' + publicationId);
        if (list && (list.style.display === 'none' || !list.style.display)) {
            list.style.display = 'block';
        }
        if (form && btn) {
            form.style.display = 'block';
            btn.style.display = 'none';
            var ta = form.querySelector('textarea');
            if (ta) ta.focus();
        }
    };

    window.cancelInlinePublicationComment = function (publicationId) {
        var form = document.getElementById('pub-comment-form-' + publicationId);
        var btn = document.getElementById('pub-show-comment-btn-' + publicationId);
        if (form && btn) {
            form.style.display = 'none';
            btn.style.display = '';
            var ta = form.querySelector('textarea');
            if (ta) ta.value = '';
        }
    };

    window.submitInlinePublicationComment = function (event, publicationId) {
        event.preventDefault();
        var form = event.target;
        var textarea = form.querySelector('textarea');
        var commentText = textarea ? textarea.value.trim() : '';
        if (!commentText) {
            alert('Please enter a comment');
            return;
        }
        var wordCount = commentText.split(/\s+/).filter(function (w) { return w.length > 0; }).length;
        if (wordCount > 300) {
            alert('Comments are limited to 300 words.');
            return;
        }

        var submitBtn = form.querySelector('button[type="submit"]');
        var originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i>Posting...';

        var formData = new FormData(form);
        formData.set('publication_id', String(publicationId));
        formData.set('comment', commentText);

        fetch('{{ url('records/comment') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(function (response) {
            if (!response.ok) {
                return response.json().then(function (j) {
                    return Promise.reject(j);
                }, function () {
                    return Promise.reject({ error: 'Request failed.' });
                });
            }
            return response.json();
        })
        .then(function (data) {
            if (!data.success) {
                throw data;
            }
            var list = document.getElementById('pub-comments-list-' + publicationId);
            if (list && data.comment_html) {
                list.style.display = 'block';
                var empty = list.querySelector('.no-comments');
                if (empty) empty.remove();
                list.insertAdjacentHTML('afterbegin', data.comment_html);
            }
            var countEl = document.querySelector('.pub-comment-count-' + publicationId);
            if (countEl && typeof data.comment_count !== 'undefined') {
                countEl.textContent = data.comment_count;
            }
            window.cancelInlinePublicationComment(publicationId);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            if (data.pending_approval) {
                alert(data.message || 'Comment submitted and awaiting approval.');
            }
        })
        .catch(function (err) {
            alert((err && (err.error || err.message)) || 'Failed to post comment.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    };

    document.addEventListener('click', function (e) {
        var copyBtn = e.target.closest('.copy-link-btn');
        if (copyBtn) {
            e.preventDefault();
            copyShareLink(copyBtn.getAttribute('data-share-url'));
        }
    });
})();
</script>
