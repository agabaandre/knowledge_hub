/**
 * Khub search AI assistant + follow-up chat (loaded on records search page).
 */
(function () {
    function mountAiSearchChatPanel(scope) {
        var container = scope || document;
        var panel = container.querySelector ? container.querySelector('#aiSearchChatPanel') : null;
        if (!panel || panel.dataset.initialized === '1') {
            return;
        }
        panel.dataset.initialized = '1';

        var strings = window.KHUB_SEARCH_AI_STRINGS || {};
        var dataRoot = panel.closest('#khubSearchAiAssistant') || panel;
        var chatUrl = dataRoot.getAttribute('data-chat-url');
        var resetUrl = dataRoot.getAttribute('data-reset-url');
        var term = dataRoot.getAttribute('data-term') || '';
        var baseQuery = {};
        try {
            baseQuery = JSON.parse(dataRoot.getAttribute('data-query') || '{}');
        } catch (e) {
            baseQuery = {};
        }

        var messagesEl = panel.querySelector('#aiSearchChatMessages');
        var emptyEl = panel.querySelector('#aiSearchChatEmpty');
        var form = panel.querySelector('#aiSearchChatForm');
        var input = panel.querySelector('#aiSearchChatInput');
        var sendBtn = panel.querySelector('#aiSearchChatSend');
        var resetBtn = panel.querySelector('#aiSearchChatReset');
        if (!form || !input || !sendBtn || !resetBtn || !chatUrl || !resetUrl) {
            return;
        }

        var conversationId = sessionStorage.getItem('khubAiSearchChatId') || '';
        var csrf = document.querySelector('meta[name="csrf-token"]');
        var csrfToken = csrf ? csrf.getAttribute('content') : '';

        function appendBubble(role, text, documents) {
            if (emptyEl) {
                emptyEl.style.display = 'none';
            }
            var bubble = document.createElement('div');
            bubble.className = 'ai-search-chat__bubble ai-search-chat__bubble--' + role;
            bubble.textContent = text;

            if (role === 'assistant' && documents && documents.length) {
                var docs = document.createElement('div');
                docs.className = 'ai-search-chat__docs';
                var title = document.createElement('div');
                title.className = 'ai-search-chat__docs-title';
                title.textContent = strings.chatDocuments || 'Related documents';
                docs.appendChild(title);
                documents.forEach(function (doc) {
                    if (!doc.url) {
                        return;
                    }
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
            sendBtn.textContent = loading
                ? (strings.chatThinking || 'Analyzing…')
                : (strings.chatSend || 'Send');
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var message = (input.value || '').trim();
            if (message.length < 2) {
                return;
            }

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
                .then(function (r) {
                    return r.json().then(function (data) {
                        return { ok: r.ok, data: data };
                    });
                })
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
                    appendBubble('assistant', strings.chatError || 'Could not reach Khub AI.');
                })
                .finally(function () {
                    setLoading(false);
                });
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
                empty.textContent = strings.chatSubtitle || '';
                messagesEl.appendChild(empty);
            });
        });
    }

    function initKhubSearchAssistant(root) {
        if (!root || root.dataset.initialized === '1') {
            return;
        }
        root.dataset.initialized = '1';

        var panel = root.querySelector('#khubSearchAiPanel');
        var toggleBtn = root.querySelector('#khubSearchAiToggle');
        var viewResourcesBtn = root.querySelector('#khubSearchAiViewResources');
        var chatInput = root.querySelector('#aiSearchChatInput');

        function setOpen(open, focusTarget) {
            root.classList.toggle('is-open', open);
            if (panel) {
                panel.hidden = !open;
            }
            if (toggleBtn) {
                toggleBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            }
            if (open && focusTarget === 'chat' && chatInput) {
                setTimeout(function () {
                    chatInput.focus();
                }, 120);
            }
            if (open && focusTarget === 'resources') {
                var anchor = root.querySelector('#khubSearchAiResources');
                if (anchor) {
                    setTimeout(function () {
                        anchor.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 120);
                }
            }
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                var willOpen = !root.classList.contains('is-open');
                setOpen(willOpen, willOpen ? 'chat' : null);
            });
        }

        if (viewResourcesBtn) {
            viewResourcesBtn.addEventListener('click', function () {
                setOpen(true, 'resources');
                var showAllBtn = root.querySelector('#khubSearchAiShowAllDocs');
                if (showAllBtn) {
                    showAllBtn.click();
                }
            });
        }

        root.querySelectorAll('.js-khub-search-ai-chip').forEach(function (chip) {
            chip.addEventListener('click', function () {
                var prompt = chip.getAttribute('data-prompt') || '';
                setOpen(true, 'chat');
                if (chatInput && prompt) {
                    chatInput.value = prompt;
                    chatInput.focus();
                }
            });
        });

        mountAiSearchChatPanel(root);
    }

    window.initAiSearchChat = function (scope) {
        mountAiSearchChatPanel(scope || document);
    };

    window.initKhubSearchAssistant = function (scope) {
        var root = (scope || document).querySelector('#khubSearchAiAssistant');
        if (root) {
            initKhubSearchAssistant(root);
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        window.initKhubSearchAssistant(document);
    });
})();
