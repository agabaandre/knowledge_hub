@php
    $aiChatTerm = trim((string) request('term', ''));
    $aiChatQuery = request()->except('page');
@endphp
<style>
    .ai-search-chat {
        border: 1px solid #dbeafe;
        border-radius: 0.35rem;
        background: linear-gradient(180deg, #f8fbff 0%, #fff 100%);
        margin-bottom: 0.75rem;
        font-size: 0.84rem;
    }
    .ai-search-chat__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.65rem 0.85rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .ai-search-chat__title { margin: 0; font-size: 0.92rem; font-weight: 700; color: #0f172a; }
    .ai-search-chat__subtitle { margin: 0.15rem 0 0; font-size: 0.72rem; color: #64748b; }
    .ai-search-chat__messages {
        max-height: 320px;
        overflow-y: auto;
        padding: 0.75rem 0.85rem;
        display: flex;
        flex-direction: column;
        gap: 0.55rem;
    }
    .ai-search-chat__bubble {
        max-width: 92%;
        padding: 0.55rem 0.7rem;
        border-radius: 0.45rem;
        line-height: 1.45;
        white-space: pre-wrap;
        word-break: break-word;
    }
    .ai-search-chat__bubble--user {
        align-self: flex-end;
        background: var(--theme-color-primary, #119A48);
        color: #fff;
    }
    .ai-search-chat__bubble--assistant {
        align-self: flex-start;
        background: #fff;
        border: 1px solid #e2e8f0;
        color: #334155;
    }
    .ai-search-chat__docs {
        margin-top: 0.45rem;
        padding-top: 0.45rem;
        border-top: 1px dashed #e2e8f0;
    }
    .ai-search-chat__docs-title {
        font-size: 0.64rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 0.25rem;
    }
    .ai-search-chat__doc-link {
        display: block;
        font-size: 0.75rem;
        color: var(--theme-color-primary, #119A48);
        text-decoration: none;
        margin-bottom: 0.15rem;
    }
    .ai-search-chat__doc-link:hover { text-decoration: underline; }
    .ai-search-chat__form {
        display: flex;
        gap: 0.45rem;
        padding: 0.65rem 0.85rem 0.75rem;
        border-top: 1px solid #e2e8f0;
    }
    .ai-search-chat__input {
        flex: 1;
        min-height: 42px;
        resize: vertical;
    }
    .ai-search-chat__actions { display: flex; flex-direction: column; gap: 0.35rem; }
    .ai-search-chat__empty {
        color: #94a3b8;
        font-size: 0.78rem;
        text-align: center;
        padding: 0.5rem 0;
    }
</style>

<div class="ai-search-chat" id="aiSearchChatPanel"
     data-chat-url="{{ route('records.search.ai-chat') }}"
     data-reset-url="{{ route('records.search.ai-chat.reset') }}"
     data-term="{{ e($aiChatTerm) }}"
     data-query='@json($aiChatQuery)'>
    <div class="ai-search-chat__head">
        <div>
            <h3 class="ai-search-chat__title">{{ __('publications.search.ai_chat_title') }}</h3>
            <p class="ai-search-chat__subtitle">{{ __('publications.search.ai_chat_subtitle') }}</p>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="aiSearchChatReset">{{ __('publications.search.ai_chat_reset') }}</button>
    </div>
    <div class="ai-search-chat__messages" id="aiSearchChatMessages">
        <div class="ai-search-chat__empty" id="aiSearchChatEmpty">{{ __('publications.search.ai_chat_subtitle') }}</div>
    </div>
    <form class="ai-search-chat__form" id="aiSearchChatForm">
        @csrf
        <textarea class="form-control ai-search-chat__input" id="aiSearchChatInput" rows="2" maxlength="2000" placeholder="{{ __('publications.search.ai_chat_placeholder') }}" required></textarea>
        <div class="ai-search-chat__actions">
            <button type="submit" class="btn btn-sm theme-bg text-white" id="aiSearchChatSend">{{ __('publications.search.ai_chat_send') }}</button>
        </div>
    </form>
</div>

<script>
(function () {
    function initAiSearchChat(root) {
        if (!root || root.dataset.initialized === '1') return;
        root.dataset.initialized = '1';

        var chatUrl = root.getAttribute('data-chat-url');
        var resetUrl = root.getAttribute('data-reset-url');
        var term = root.getAttribute('data-term') || '';
        var baseQuery = {};
        try { baseQuery = JSON.parse(root.getAttribute('data-query') || '{}'); } catch (e) { baseQuery = {}; }

        var messagesEl = root.querySelector('#aiSearchChatMessages');
        var emptyEl = root.querySelector('#aiSearchChatEmpty');
        var form = root.querySelector('#aiSearchChatForm');
        var input = root.querySelector('#aiSearchChatInput');
        var sendBtn = root.querySelector('#aiSearchChatSend');
        var resetBtn = root.querySelector('#aiSearchChatReset');
        var conversationId = sessionStorage.getItem('khubAiSearchChatId') || '';
        var csrf = document.querySelector('meta[name="csrf-token"]');
        var csrfToken = csrf ? csrf.getAttribute('content') : '';

        function appendBubble(role, text, documents) {
            if (emptyEl) emptyEl.style.display = 'none';
            var bubble = document.createElement('div');
            bubble.className = 'ai-search-chat__bubble ai-search-chat__bubble--' + role;
            bubble.textContent = text;

            if (role === 'assistant' && documents && documents.length) {
                var docs = document.createElement('div');
                docs.className = 'ai-search-chat__docs';
                var title = document.createElement('div');
                title.className = 'ai-search-chat__docs-title';
                title.textContent = @json(__('publications.search.ai_chat_documents'));
                docs.appendChild(title);
                documents.forEach(function (doc) {
                    if (!doc.url) return;
                    var link = document.createElement('a');
                    link.className = 'ai-search-chat__doc-link';
                    link.href = doc.url;
                    link.target = '_blank';
                    link.rel = 'noopener noreferrer';
                    link.textContent = doc.title || doc.url;
                    docs.appendChild(link);
                });
                bubble.appendChild(docs);
            }

            messagesEl.appendChild(bubble);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function setLoading(loading) {
            sendBtn.disabled = loading;
            input.disabled = loading;
            sendBtn.textContent = loading ? @json(__('publications.search.ai_chat_thinking')) : @json(__('publications.search.ai_chat_send'));
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var message = (input.value || '').trim();
            if (message.length < 2) return;

            appendBubble('user', message);
            input.value = '';
            setLoading(true);

            var payload = Object.assign({}, baseQuery, {
                term: term,
                message: message,
                conversation_id: conversationId
            });

            fetch(chatUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload)
            })
                .then(function (r) { return r.json().then(function (data) { return { ok: r.ok, data: data }; }); })
                .then(function (result) {
                    if (!result.ok || !result.data.ok) {
                        throw new Error((result.data && result.data.error) || 'error');
                    }
                    conversationId = result.data.conversation_id || conversationId;
                    if (conversationId) {
                        sessionStorage.setItem('khubAiSearchChatId', conversationId);
                    }
                    appendBubble('assistant', result.data.reply || '', result.data.documents || []);
                })
                .catch(function () {
                    appendBubble('assistant', @json(__('publications.search.ai_chat_error')));
                })
                .finally(function () { setLoading(false); });
        });

        resetBtn.addEventListener('click', function () {
            fetch(resetUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                credentials: 'same-origin',
                body: JSON.stringify({ conversation_id: conversationId })
            }).finally(function () {
                conversationId = '';
                sessionStorage.removeItem('khubAiSearchChatId');
                messagesEl.innerHTML = '';
                var empty = document.createElement('div');
                empty.className = 'ai-search-chat__empty';
                empty.textContent = @json(__('publications.search.ai_chat_subtitle'));
                messagesEl.appendChild(empty);
            });
        });
    }

    window.initAiSearchChat = function (scope) {
        var root = (scope || document).querySelector('#aiSearchChatPanel');
        initAiSearchChat(root);
    };

    document.addEventListener('DOMContentLoaded', function () {
        window.initAiSearchChat(document);
    });
})();
</script>
