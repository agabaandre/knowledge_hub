<script>
(function () {
  var ctx = typeof window.khubAiChat === 'object' && window.khubAiChat !== null ? window.khubAiChat : null;
  var chatType = ctx && ctx.type === 'forums_index' ? 'forums_index' : (ctx && ctx.type === 'forum' ? 'forum' : 'publication');
  var publicationId = ctx && ctx.publication_id != null && ctx.publication_id !== ''
    ? parseInt(ctx.publication_id, 10)
    : (typeof pdfChatPublicationId !== 'undefined' ? pdfChatPublicationId : null);
  var forumId = ctx && ctx.forum_id != null && ctx.forum_id !== '' ? parseInt(ctx.forum_id, 10) : null;
  var forumsIndexForumIds = Array.isArray(ctx && ctx.forum_ids) ? ctx.forum_ids.map(function (id) { return parseInt(id, 10); }).filter(function (id) { return !isNaN(id); }) : (Array.isArray(window.forumsIndexForumIds) ? window.forumsIndexForumIds : []);
  if (forumId !== null && isNaN(forumId)) forumId = null;
  if (publicationId !== null && isNaN(publicationId)) publicationId = null;

  var attachmentId = typeof pdfChatAttachmentId !== 'undefined' ? pdfChatAttachmentId : null;
  var pdfChatAssistantMode = typeof window.pdfChatAssistantMode !== 'undefined' ? window.pdfChatAssistantMode : 'auto';
  var pdfSources = Array.isArray(window.pdfChatPdfSources) ? window.pdfChatPdfSources : [];
  var selectedPdfSourceKeys = Array.isArray(window.pdfChatPdfSourceKeys) ? window.pdfChatPdfSourceKeys.slice() : [];
  var activeSourceLabel = null;
  if (chatType === 'forum') {
    pdfChatAssistantMode = 'forum';
  }
  var sessionId = null;
  var sourceId = null;
  var isStreaming = false;

  window.openPdfChat = function (pubId, attId, docTitle, assistantModeOpt) {
    chatType = 'publication';
    forumId = null;
    publicationId = pubId;
    attachmentId = attId === undefined || attId === '' ? null : attId;
    if (assistantModeOpt === 'publication' || assistantModeOpt === 'chatpdf' || assistantModeOpt === 'auto') {
      pdfChatAssistantMode = assistantModeOpt;
    } else if (typeof window.pdfChatAssistantMode !== 'undefined') {
      pdfChatAssistantMode = window.pdfChatAssistantMode;
    } else {
      pdfChatAssistantMode = 'auto';
    }
    sessionId = null;
    sourceId = null;
    if (Array.isArray(window.pdfChatPdfSourceKeys) && window.pdfChatPdfSourceKeys.length) {
      selectedPdfSourceKeys = window.pdfChatPdfSourceKeys.map(String);
    } else if (attId !== undefined && attId !== null && attId !== '') {
      selectedPdfSourceKeys = [String(attId)];
    } else {
      selectedPdfSourceKeys = [];
    }
    syncAttachmentIdFromSelectedKeys();
    var titleEl = document.getElementById('pdf-chat-doc-title');
    if (titleEl) titleEl.textContent = (typeof docTitle === 'string' && docTitle) ? docTitle : (typeof pdfChatDocumentTitle !== 'undefined' ? pdfChatDocumentTitle : 'Document');
    setupWelcomeForMode(pdfChatAssistantMode === 'forum' ? 'forum' : (pdfChatAssistantMode === 'chatpdf' ? 'chatpdf' : 'publication'));
    var modalEl = document.getElementById('pdf-chat-modal');
    if (!modalEl) {
      if (typeof loadPdfChatSession === 'function') loadPdfChatSession();
      return;
    }
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
      bootstrap.Modal.getOrCreateInstance(modalEl).show();
    } else if (typeof $ !== 'undefined' && $.fn.modal) {
      $(modalEl).modal('show');
    } else {
      modalEl.classList.add('show');
      modalEl.style.display = 'block';
      modalEl.setAttribute('aria-hidden', 'false');
      document.body.classList.add('modal-open');
      var backdrop = document.createElement('div');
      backdrop.className = 'modal-backdrop fade show';
      backdrop.id = 'pdf-chat-modal-backdrop';
      document.body.appendChild(backdrop);
    }
    if (typeof loadPdfChatSession === 'function') loadPdfChatSession();
  };

  window.openForumsListingAssistant = function (forumIds, pageTitle, initialPrompt) {
    chatType = 'forums_index';
    forumId = null;
    publicationId = null;
    attachmentId = null;
    pdfChatAssistantMode = 'forums_index';
    forumsIndexForumIds = Array.isArray(forumIds) ? forumIds.map(function (id) { return parseInt(id, 10); }).filter(function (id) { return !isNaN(id); }) : [];
    sessionId = null;
    sourceId = null;
    var titleEl = document.getElementById('pdf-chat-doc-title');
    if (titleEl) {
      titleEl.textContent = (typeof pageTitle === 'string' && pageTitle) ? pageTitle : 'Discussion forums';
    }
    setupWelcomeForMode('forums_index');
    var modalEl = document.getElementById('pdf-chat-modal');
    if (!modalEl) {
      if (typeof loadPdfChatSession === 'function') loadPdfChatSession();
      return;
    }
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
      bootstrap.Modal.getOrCreateInstance(modalEl).show();
    } else if (typeof $ !== 'undefined' && $.fn.modal) {
      $(modalEl).modal('show');
    } else {
      modalEl.classList.add('show');
      modalEl.style.display = 'block';
      modalEl.setAttribute('aria-hidden', 'false');
      document.body.classList.add('modal-open');
      var backdrop = document.createElement('div');
      backdrop.className = 'modal-backdrop fade show';
      backdrop.id = 'pdf-chat-modal-backdrop';
      document.body.appendChild(backdrop);
    }
    if (typeof loadPdfChatSession === 'function') {
      loadPdfChatSession().then(function () {
        if (initialPrompt && typeof sendMessageWithText === 'function') {
          sendMessageWithText(initialPrompt);
        }
      });
    }
  };

  window.openForumAssistant = function (fId, threadTitle) {
    chatType = 'forum';
    forumId = typeof fId === 'number' ? fId : parseInt(fId, 10);
    if (isNaN(forumId)) return;
    publicationId = null;
    attachmentId = null;
    pdfChatAssistantMode = 'forum';
    sessionId = null;
    sourceId = null;
    var titleEl = document.getElementById('pdf-chat-doc-title');
    if (titleEl) {
      titleEl.textContent = (typeof threadTitle === 'string' && threadTitle)
        ? threadTitle
        : (typeof pdfChatDocumentTitle !== 'undefined' ? pdfChatDocumentTitle : 'Forum thread');
    }
    setupWelcomeForMode('forum');
    var modalEl = document.getElementById('pdf-chat-modal');
    if (!modalEl) {
      if (typeof loadPdfChatSession === 'function') loadPdfChatSession();
      return;
    }
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
      bootstrap.Modal.getOrCreateInstance(modalEl).show();
    } else if (typeof $ !== 'undefined' && $.fn.modal) {
      $(modalEl).modal('show');
    } else {
      modalEl.classList.add('show');
      modalEl.style.display = 'block';
      modalEl.setAttribute('aria-hidden', 'false');
      document.body.classList.add('modal-open');
      var backdrop = document.createElement('div');
      backdrop.className = 'modal-backdrop fade show';
      backdrop.id = 'pdf-chat-modal-backdrop';
      document.body.appendChild(backdrop);
    }
    if (typeof loadPdfChatSession === 'function') loadPdfChatSession();
  };

  function initPdfChatDelegate() {
    document.body.addEventListener('click', function (e) {
      var fBtn = e.target.closest('.js-open-forum-assistant');
      if (fBtn) {
        e.preventDefault();
        e.stopPropagation();
        var fid = parseInt(fBtn.getAttribute('data-forum-id'), 10);
        if (isNaN(fid)) return;
        var t = fBtn.getAttribute('data-thread-title') || (typeof pdfChatDocumentTitle !== 'undefined' ? pdfChatDocumentTitle : '');
        openForumAssistant(fid, t);
        return;
      }
      var btn = e.target.closest('.js-open-pdf-chat');
      if (!btn) return;
      e.preventDefault();
      e.stopPropagation();
      var pubId = parseInt(btn.getAttribute('data-publication-id'), 10);
      if (isNaN(pubId)) return;
      var attId = btn.getAttribute('data-attachment-id');
      if (attId === '' || attId === null || attId === undefined) attId = null;
      else { attId = parseInt(attId, 10); if (isNaN(attId)) attId = null; }
      var docTitle = btn.getAttribute('data-doc-title') || (typeof pdfChatDocumentTitle !== 'undefined' ? pdfChatDocumentTitle : 'Document');
      var am = btn.getAttribute('data-assistant-mode');
      openPdfChat(pubId, attId, docTitle, am || undefined);
    }, true);
  }
  function runPdfChatDelegate() {
    if (document.body) initPdfChatDelegate();
    else document.addEventListener('DOMContentLoaded', initPdfChatDelegate);
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', runPdfChatDelegate);
  } else {
    runPdfChatDelegate();
  }

  function getToken() {
    var m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute('content') : '';
  }

  function messagesEl() {
    return document.getElementById('pdf-chat-messages');
  }

  function scrollEl() {
    return document.getElementById('pdf-chat-scroll');
  }

  function welcomeEl() {
    return document.getElementById('pdf-chat-welcome');
  }

  function suggestionsEl() {
    return document.getElementById('pdf-chat-suggestions');
  }

  function scrollChatToBottom() {
    var sc = scrollEl();
    if (sc) sc.scrollTop = sc.scrollHeight;
  }

  function setWelcomeVisible(visible) {
    var w = welcomeEl();
    var s = suggestionsEl();
    if (w) {
      w.classList.toggle('is-hidden', !visible);
      w.setAttribute('aria-hidden', visible ? 'false' : 'true');
    }
    if (s) {
      s.classList.toggle('is-hidden', !visible);
    }
  }

  function updateModeBadge(mode) {
    var badge = document.getElementById('pdf-chat-mode-badge');
    var icon = document.getElementById('pdf-chat-doc-icon');
    var hint = document.getElementById('pdf-chat-doc-hint');
    if (!badge) return;
    badge.classList.remove('forum', 'publication');
    if (icon) icon.classList.remove('is-forum', 'is-resource');
    if (mode === 'forums_index') {
      badge.textContent = 'Forums overview';
      badge.classList.add('forum');
      if (icon) { icon.classList.add('is-forum'); icon.innerHTML = '<i class="fa fa-comments"></i>'; }
      if (hint) hint.textContent = 'Answers use the discussions on this page. Ask about a specific thread to load its full detail.';
    } else if (mode === 'forum') {
      badge.textContent = 'Forum thread';
      badge.classList.add('forum');
      if (icon) { icon.classList.add('is-forum'); icon.innerHTML = '<i class="fa fa-comments"></i>'; }
      if (hint) hint.textContent = 'Summaries and answers are drawn from this discussion thread only.';
    } else if (mode === 'publication') {
      badge.textContent = 'Resource context';
      badge.classList.add('publication');
      if (icon) { icon.classList.add('is-resource'); icon.innerHTML = '<i class="fa fa-book-open"></i>'; }
      if (hint) hint.textContent = 'Answers combine the title, description, and extracted text from this resource.';
    } else {
      badge.textContent = 'PDF document';
      if (icon) icon.innerHTML = '<i class="fa fa-file-pdf"></i>';
      if (hint) {
        hint.textContent = activeSourceLabel
          ? ('Answers are grounded in: ' + activeSourceLabel + '. Select one or more PDFs above; each is uploaded to ChatPDF separately.')
          : 'Answers are grounded in the PDF attached to this resource.';
      }
    }
  }

  function sourceKey(src) {
    if (!src) return '';
    var attId = src.attachment_id;
    return attId === null || attId === undefined || attId === '' ? 'main' : String(attId);
  }

  function syncPdfSourcesFromWindow() {
    if (Array.isArray(window.pdfChatPdfSources) && window.pdfChatPdfSources.length) {
      pdfSources = window.pdfChatPdfSources;
    }
  }

  function defaultSelectedPdfSourceKeys() {
    if (selectedPdfSourceKeys.length) {
      return selectedPdfSourceKeys.slice();
    }
    if (pdfSources.length) {
      return [sourceKey(pdfSources[0])];
    }
    return [];
  }

  function keysEqual(a, b) {
    if (!Array.isArray(a) || !Array.isArray(b) || a.length !== b.length) return false;
    var sortedA = a.slice().sort();
    var sortedB = b.slice().sort();
    return sortedA.every(function (val, idx) { return val === sortedB[idx]; });
  }

  function syncAttachmentIdFromSelectedKeys() {
    if (!selectedPdfSourceKeys.length) {
      attachmentId = null;
      return;
    }
    var firstKey = selectedPdfSourceKeys[0];
    if (firstKey === 'main') {
      attachmentId = null;
      return;
    }
    var parsed = parseInt(firstKey, 10);
    attachmentId = isNaN(parsed) ? null : parsed;
  }

  function updateActiveSourceLabelFromKeys() {
    if (!selectedPdfSourceKeys.length) {
      activeSourceLabel = null;
      return;
    }
    var labels = [];
    pdfSources.forEach(function (src) {
      if (selectedPdfSourceKeys.indexOf(sourceKey(src)) !== -1) {
        labels.push(src.label || 'Document');
      }
    });
    if (!labels.length) {
      activeSourceLabel = null;
      return;
    }
    activeSourceLabel = labels.length === 1 ? labels[0] : (labels.length + ' PDF documents selected');
  }

  function updateDocSelectUi() {
    var wrap = document.getElementById('pdf-chat-doc-select-wrap');
    var select = document.getElementById('pdf-chat-doc-select');
    if (!wrap || !select) return;

    syncPdfSourcesFromWindow();
    var show = chatType === 'publication' && pdfSources.length > 1 && (pdfChatAssistantMode === 'chatpdf' || pdfChatAssistantMode === 'auto');
    wrap.classList.toggle('is-hidden', !show);
    wrap.setAttribute('aria-hidden', show ? 'false' : 'true');

    if (!show) {
      select.innerHTML = '';
      return;
    }

    if (!selectedPdfSourceKeys.length) {
      selectedPdfSourceKeys = defaultSelectedPdfSourceKeys();
    }

    select.innerHTML = '';
    pdfSources.forEach(function (src) {
      var opt = document.createElement('option');
      var key = sourceKey(src);
      opt.value = key;
      opt.textContent = src.label || 'Document';
      opt.selected = selectedPdfSourceKeys.indexOf(key) !== -1;
      select.appendChild(opt);
    });
  }

  function applyPdfDocumentSelection(fromSelect) {
    if (isStreaming) return;

    var select = fromSelect || document.getElementById('pdf-chat-doc-select');
    if (!select) return;

    var nextKeys = [];
    Array.prototype.forEach.call(select.selectedOptions || [], function (opt) {
      if (opt.value) nextKeys.push(opt.value);
    });

    if (!nextKeys.length) {
      nextKeys = defaultSelectedPdfSourceKeys();
      updateDocSelectUi();
      return;
    }

    nextKeys.sort(function (a, b) {
      var indexA = -1;
      var indexB = -1;
      pdfSources.forEach(function (src, idx) {
        var key = sourceKey(src);
        if (key === a) indexA = idx;
        if (key === b) indexB = idx;
      });
      return indexA - indexB;
    });

    if (keysEqual(nextKeys, selectedPdfSourceKeys)) {
      return;
    }

    selectedPdfSourceKeys = nextKeys;
    syncAttachmentIdFromSelectedKeys();
    updateActiveSourceLabelFromKeys();
    pdfChatAssistantMode = 'chatpdf';
    sessionId = null;
    sourceId = null;
    updateModeBadge('chatpdf');
    loadPdfChatSession();
  }

  function welcomeCopyForMode(mode) {
    if (mode === 'forums_index') {
      return {
        text: 'Explore discussions on this page. Ask for cross-thread summaries, trending topics, or name a thread to drill into its posts and comments.',
        tips: [
          'Start with “What are the main topics on this page?”',
          'Name a thread (e.g. Ebola, cholera, health sovereignty) for deeper detail.',
          'Compare engagement or themes across multiple discussions.'
        ],
        prompts: ['What are the main topics on this page?', 'Which threads are most active?', 'Summarize the Ebola discussion in detail']
      };
    }
    if (mode === 'forum') {
      return {
        text: 'Ask for a thread summary, clarifications, or follow-up questions. Khub AI uses only what appears in this discussion.',
        tips: [
          'Request a concise summary of the main arguments.',
          'Ask what questions or gaps remain in the thread.',
          'Follow up to explore a specific comment in more detail.'
        ],
        prompts: ['Summarize this discussion', 'What are the main points raised?', 'What questions remain open?']
      };
    }
    if (mode === 'publication') {
      return {
        text: 'Explore this resource with natural-language questions. Khub AI uses the title, description, and available document text.',
        tips: [
          'Request an executive summary of the resource.',
          'Ask about recommendations, findings, or definitions.',
          'Use follow-ups to drill into a specific theme.'
        ],
        prompts: ['Give me an overview', 'What are the key takeaways?', 'Summarize the main recommendations']
      };
    }
    return {
      text: 'Chat directly with the PDF document(s). Ask for summaries, section explanations, or targeted questions.',
      tips: [
        'Start with a high-level summary of the document.',
        'Select multiple PDFs above to compare language versions or related files.',
        'Ask about specific sections, figures, or data points.',
        'Request bullet-point highlights for quick reading.'
      ],
      prompts: ['Summarize this document', 'What are the main findings?', 'List the key recommendations']
    };
  }

  function renderSuggestions(prompts) {
    var container = suggestionsEl();
    if (!container) return;
    container.innerHTML = '';
    (prompts || []).forEach(function (text) {
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'khub-ai-suggestion';
      btn.textContent = text;
      btn.addEventListener('click', function () {
        var input = document.getElementById('pdf-chat-input');
        if (input) input.value = text;
        sendMessage();
      });
      container.appendChild(btn);
    });
  }

  function setupWelcomeForMode(mode) {
    var copy = welcomeCopyForMode(mode);
    var textEl = document.getElementById('pdf-chat-welcome-text');
    var tipsEl = document.getElementById('pdf-chat-welcome-tips');
    if (textEl) textEl.textContent = copy.text;
    if (tipsEl) {
      tipsEl.innerHTML = copy.tips.map(function (t) { return '<li>' + escapeHtml(t) + '</li>'; }).join('');
    }
    renderSuggestions(copy.prompts);
    updateModeBadge(mode);
    updateDocSelectUi();
  }

  function appendMessage(role, content, isStreamingPlaceholder) {
    setWelcomeVisible(false);
    var row = document.createElement('div');
    row.className = 'pdf-chat-msg-row ' + role;
    var el = document.createElement('div');
    el.className = 'pdf-chat-msg ' + role;
    var label = role === 'user' ? 'You' : 'Khub AI';
    var avatarIcon = role === 'user' ? '<i class="fa fa-user"></i>' : '<i class="fa-solid fa-microchip"></i>';
    var contentHtml = isStreamingPlaceholder ? '' : (role === 'assistant' ? formatAssistantReply(content) : escapeHtml(content));
    var showExportOnResponse = role === 'assistant' && content && !isStreamingPlaceholder;
    var actionsHtml = showExportOnResponse
      ? '<div class="pdf-chat-msg-actions"><button type="button" class="btn btn-outline-secondary btn-sm pdf-chat-export-pdf" title="Download as PDF"><i class="fa fa-file-pdf"></i> PDF</button><button type="button" class="btn btn-outline-secondary btn-sm pdf-chat-export-word" title="Export as Word"><i class="fa fa-file-word"></i> Word</button><button type="button" class="btn btn-outline-secondary btn-sm pdf-chat-share-msg" title="Share"><i class="fa fa-share-alt"></i> Share</button></div>'
      : '';
    el.innerHTML = '<div class="role-label">' + escapeHtml(label) + '</div><div class="pdf-chat-msg-bubble"><div class="content">' + contentHtml + '</div></div>' + actionsHtml;
    row.innerHTML = '<div class="pdf-chat-avatar" aria-hidden="true">' + avatarIcon + '</div>';
    row.appendChild(el);
    if (isStreamingPlaceholder) {
      el.dataset.streaming = '1';
    }
    messagesEl().appendChild(row);
    scrollChatToBottom();
    var contentEl = el.querySelector('.content');
    if (el.querySelector('.pdf-chat-export-pdf')) {
      el.querySelector('.pdf-chat-export-pdf').addEventListener('click', function() {
        var msg = this.closest('.pdf-chat-msg');
        var c = msg && msg.querySelector('.content');
        exportContent(c ? (c.innerText || c.textContent || '') : '', 'pdf');
      });
      el.querySelector('.pdf-chat-export-word').addEventListener('click', function() {
        var msg = this.closest('.pdf-chat-msg');
        var c = msg && msg.querySelector('.content');
        exportContent(c ? (c.innerText || c.textContent || '') : '', 'word');
      });
      el.querySelector('.pdf-chat-share-msg').addEventListener('click', function() {
        var msg = this.closest('.pdf-chat-msg');
        var c = msg && msg.querySelector('.content');
        var text = c ? (c.innerText || c.textContent || '').trim() : '';
        shareContent(text, getDocumentTitle(), this);
      });
    }
    return contentEl;
  }

  function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  function markdownToHtml(text) {
    if (!text) return '';
    var s = String(text)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    s = s.replace(/^#### (.+)$/gm, '<h4>$1</h4>');
    s = s.replace(/^### (.+)$/gm, '<h3>$1</h3>');
    s = s.replace(/^## (.+)$/gm, '<h2>$1</h2>');
    s = s.replace(/^# (.+)$/gm, '<h1>$1</h1>');
    s = s.replace(/^---\s*$/gm, '<hr>');
    s = s.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    s = s.replace(/\*(.+?)\*/g, '<em>$1</em>');
    s = s.replace(/^-\s+(.+)$/gm, '<li>$1</li>');
    s = s.replace(/(<li>.*?<\/li>(\n?|$))+/g, function(m) { return '<ul>' + m + '</ul>'; });
    s = s.replace(/\n\n+/g, '</p><p>');
    s = s.replace(/\n/g, '<br>');
    if (s.indexOf('<p>') !== 0) s = '<p>' + s + '</p>';
    return s.replace(/<p><\/p>/g, '').replace(/<p>(<h[1-4]>)/g, '$1').replace(/(<\/h[1-4]>)<\/p>/g, '$1').replace(/<p>(<ul>)/g, '$1').replace(/(<\/ul>)<\/p>/g, '$1');
  }

  /** True when the model returned HTML (legacy prompts); must not run through markdownToHtml's escape step. */
  function looksLikeAssistantHtml(text) {
    return /<\s*\/?(p|ul|ol|li|h[1-6]|div|strong|em|b|i|br|hr|a)\b/i.test(String(text));
  }

  function sanitizeAssistantHtml(html) {
    var allowed = { P: 1, UL: 1, OL: 1, LI: 1, H3: 1, H4: 1, H2: 1, STRONG: 1, EM: 1, B: 1, I: 1, BR: 1, HR: 1, A: 1, DIV: 1, SPAN: 1 };
    var doc = new DOMParser().parseFromString('<div class="pdf-chat-san-wrap">' + String(html) + '</div>', 'text/html');
    var wrap = doc.body.querySelector('.pdf-chat-san-wrap');
    if (!wrap) return escapeHtml(html);
    function cleanNode(node) {
      var child = node.firstChild;
      while (child) {
        var next = child.nextSibling;
        if (child.nodeType === 1) {
          var tag = child.tagName;
          if (!allowed[tag]) {
            while (child.firstChild) node.insertBefore(child.firstChild, child);
            node.removeChild(child);
          } else {
            var attrs = child.attributes;
            for (var i = attrs.length - 1; i >= 0; i--) {
              var nm = attrs[i].name.toLowerCase();
              if (nm.indexOf('on') === 0) {
                child.removeAttribute(attrs[i].name);
              } else if (tag === 'A') {
                if (nm !== 'href' && nm !== 'target' && nm !== 'rel') child.removeAttribute(attrs[i].name);
              } else if (nm === 'style' || nm === 'id') {
                child.removeAttribute(attrs[i].name);
              }
            }
            if (tag === 'A') {
              var href = child.getAttribute('href') || '';
              if (!/^https?:\/\//i.test(href)) {
                child.removeAttribute('href');
              } else {
                child.setAttribute('rel', 'noopener noreferrer');
                child.setAttribute('target', '_blank');
              }
            }
            cleanNode(child);
          }
        } else if (child.nodeType !== 3 && child.nodeType !== 1 && child.nodeType !== 8) {
          node.removeChild(child);
        }
        child = next;
      }
    }
    cleanNode(wrap);
    return wrap.innerHTML;
  }

  function formatAssistantReply(text) {
    if (!text) return '';
    if (looksLikeAssistantHtml(text)) {
      return sanitizeAssistantHtml(text);
    }
    return markdownToHtml(text);
  }

  function setStreamingContent(contentEl, text) {
    contentEl.textContent = text;
    scrollChatToBottom();
  }

  function finalizeStreamingMessage(contentEl) {
    var text = contentEl.textContent || '';
    contentEl.innerHTML = formatAssistantReply(text);
    var msgDiv = contentEl.closest('.pdf-chat-msg');
    if (msgDiv && text) {
      var btn = '<div class="pdf-chat-msg-actions"><button type="button" class="btn btn-outline-secondary btn-sm pdf-chat-export-pdf" title="Download as PDF"><i class="fa fa-file-pdf"></i> PDF</button><button type="button" class="btn btn-outline-secondary btn-sm pdf-chat-export-word" title="Export as Word"><i class="fa fa-file-word"></i> Word</button><button type="button" class="btn btn-outline-secondary btn-sm pdf-chat-share-msg" title="Share"><i class="fa fa-share-alt"></i> Share</button></div>';
      var wrap = document.createElement('div');
      wrap.innerHTML = btn;
      msgDiv.appendChild(wrap.firstChild);
      var contentForExport = text;
      var docTitle = getDocumentTitle();
      msgDiv.querySelector('.pdf-chat-export-pdf').addEventListener('click', function() { exportContent(contentForExport, 'pdf'); });
      msgDiv.querySelector('.pdf-chat-export-word').addEventListener('click', function() { exportContent(contentForExport, 'word'); });
      msgDiv.querySelector('.pdf-chat-share-msg').addEventListener('click', function() { shareContent(contentForExport, docTitle, this); });
    }
  }

  function getDocumentTitle() {
    var el = document.getElementById('pdf-chat-doc-title');
    return (el && el.textContent) ? el.textContent.trim() : 'Chat export';
  }

  function shareContent(text, title, anchorEl) {
    var url = window.location.href;
    var shareText = (text && text.length > 200) ? text.substring(0, 197) + '...' : (text || '');
    var fullBody = (title ? title + '\n\n' : '') + shareText + (shareText ? '\n\n' : '') + url;

    function tryNativeShare() {
      if (typeof navigator !== 'undefined' && navigator.share) {
        return navigator.share({
          title: title || 'Chat export',
          text: shareText || title || 'View this chat',
          url: url
        }).then(function() { return true; }).catch(function() { return false; });
      }
      return Promise.resolve(false);
    }

    function openShareMenu() {
      var existing = document.getElementById('pdf-chat-share-dropdown');
      if (existing) existing.remove();

      var drop = document.createElement('div');
      drop.id = 'pdf-chat-share-dropdown';
      drop.className = 'pdf-chat-share-dropdown';

      var twitterText = (title ? title + ' ' : '') + url;
      if (shareText) twitterText = (shareText.length > 100 ? shareText.substring(0, 97) + '...' : shareText) + ' ' + url;
      var twitterUrl = 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(twitterText);
      var linkedInUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(url);
      var facebookUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url);
      var mailUrl = 'mailto:?subject=' + encodeURIComponent(title || 'Chat export') + '&body=' + encodeURIComponent(fullBody);

      drop.innerHTML =
        '<a href="' + twitterUrl + '" target="_blank" rel="noopener" title="Share on X (Twitter)"><i class="fab fa-twitter"></i> X (Twitter)</a>' +
        '<a href="' + linkedInUrl + '" target="_blank" rel="noopener" title="Share on LinkedIn"><i class="fab fa-linkedin"></i> LinkedIn</a>' +
        '<a href="' + facebookUrl + '" target="_blank" rel="noopener" title="Share on Facebook"><i class="fab fa-facebook"></i> Facebook</a>' +
        '<button type="button" class="pdf-chat-share-copy" title="Copy link and text"><i class="fa fa-copy"></i> Copy</button>' +
        '<a href="' + mailUrl + '" title="Email"><i class="fa fa-envelope"></i> Email</a>';

      document.body.appendChild(drop);

      var rect = anchorEl.getBoundingClientRect();
      drop.style.position = 'fixed';
      drop.style.left = Math.max(8, Math.min(rect.left, window.innerWidth - 200)) + 'px';
      drop.style.top = (rect.bottom + 4) + 'px';

      drop.querySelector('.pdf-chat-share-copy').addEventListener('click', function() {
        try {
          navigator.clipboard.writeText(fullBody);
          var t = this.innerHTML; this.innerHTML = '<i class="fa fa-check"></i> Copied'; setTimeout(function() { this.innerHTML = t; }.bind(this), 1500);
        } catch (e) { alert('Copy failed.'); }
      });

      function closeMenu() {
        drop.remove();
        document.removeEventListener('click', closeMenu);
      }
      setTimeout(function() { document.addEventListener('click', closeMenu); }, 0);
    }

    tryNativeShare().then(function(used) {
      if (!used) openShareMenu();
    });
  }

  function shareConversation() {
    var title = getDocumentTitle();
    shareContent('', title, document.getElementById('pdf-chat-share-all'));
  }

  function getAllMessagesForExport() {
    var msgs = [];
    messagesEl().querySelectorAll('.pdf-chat-msg').forEach(function(el) {
      var role = el.classList.contains('user') ? 'user' : 'assistant';
      var contentEl = el.querySelector('.content');
      var text = contentEl ? (contentEl.innerText || contentEl.textContent || '') : '';
      if (text) msgs.push({ role: role, content: text });
    });
    return msgs;
  }

  function downloadBlob(blob, filename) {
    var a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = filename;
    a.click();
    URL.revokeObjectURL(a.href);
  }

  function safeExportFilename(title, ext) {
    return (String(title).replace(/[^\w\s\-]/g, '').replace(/\s+/g, '-').substring(0, 80) || 'export') + ext;
  }

  function exportContent(content, format) {
    if (!content || !String(content).trim()) { alert('No content to export.'); return; }
    var title = getDocumentTitle();
    var url = format === 'word' ? '{{ url("ai/pdf-chat/export-word") }}' : '{{ url("ai/pdf-chat/export-pdf") }}';
    var body = JSON.stringify({ title: title, content: content });
    fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getToken(), 'Accept': 'application/json' },
      body: body
    }).then(function(r) {
      if (!r.ok) return r.json().then(function(j) { throw new Error(j.error || 'Export failed'); });
      return r.blob();
    }).then(function(blob) {
      downloadBlob(blob, safeExportFilename(title, format === 'word' ? '.docx' : '.pdf'));
    }).catch(function(err) { alert(err.message || 'Export failed. Please try again.'); });
  }

  function exportAll(format) {
    var messages = getAllMessagesForExport();
    if (!messages.length) { alert('No messages to export.'); return; }
    var title = getDocumentTitle();
    var url = format === 'word' ? '{{ url("ai/pdf-chat/export-word") }}' : '{{ url("ai/pdf-chat/export-pdf") }}';
    var body = JSON.stringify({ title: title, all_messages: messages });
    fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getToken(), 'Accept': 'application/json' },
      body: body
    }).then(function(r) {
      if (!r.ok) return r.json().then(function(j) { throw new Error(j.error || 'Export failed'); });
      return r.blob();
    }).then(function(blob) {
      downloadBlob(blob, safeExportFilename(title, format === 'word' ? '.docx' : '.pdf'));
    }).catch(function(err) { alert(err.message || 'Export failed. Please try again.'); });
  }

  function showError(msg) {
    setWelcomeVisible(false);
    var el = document.createElement('div');
    el.className = 'pdf-chat-error';
    el.innerHTML = '<i class="fa fa-circle-exclamation" aria-hidden="true"></i><span>' + escapeHtml(msg) + '</span>';
    messagesEl().appendChild(el);
    scrollChatToBottom();
  }

  function loadPdfChatSession() {
    var container = messagesEl();
    setWelcomeVisible(false);
    container.innerHTML = '<div class="pdf-chat-loading"><span class="spinner-border" role="status" aria-hidden="true"></span><p>Preparing your assistant session…</p></div>';

    if (chatType === 'forum' && !forumId) {
      container.innerHTML = '';
      showError('Forum context missing. Reload the page and try again.');
      return;
    }
    if (chatType === 'forums_index' && (!forumsIndexForumIds || !forumsIndexForumIds.length)) {
      container.innerHTML = '';
      showError('No discussions are available on this page yet.');
      return;
    }
    if (chatType === 'publication' && !publicationId) {
      container.innerHTML = '';
      showError('Resource context missing. Reload the page and try again.');
      return;
    }

    var sessionPayload = chatType === 'forums_index'
      ? { assistant_mode: 'forums_index', forum_ids: forumsIndexForumIds }
      : (chatType === 'forum'
        ? { forum_id: forumId, assistant_mode: 'forum' }
        : {
            publication_id: publicationId,
            attachment_id: attachmentId || null,
            pdf_source_keys: selectedPdfSourceKeys.length ? selectedPdfSourceKeys : defaultSelectedPdfSourceKeys(),
            assistant_mode: pdfChatAssistantMode || 'auto'
          });

    return fetch('{{ url("ai/pdf-chat/session") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getToken(),
        'Accept': 'application/json'
      },
      body: JSON.stringify(sessionPayload)
    })
      .then(function (r) {
        return r.json().catch(function () { return {}; }).then(function (data) {
          if (!r.ok) {
            throw new Error((data && data.error) ? data.error : 'Could not start chat. Please try again.');
          }
          return data;
        });
      })
      .then(function (data) {
        if (data.error) {
          container.innerHTML = '';
          showError(data.error);
          return;
        }
        sessionId = data.session_id;
        sourceId = data.source_id;
        if (data.assistant_mode === 'publication' || data.assistant_mode === 'chatpdf' || data.assistant_mode === 'forum' || data.assistant_mode === 'forums_index') {
          pdfChatAssistantMode = data.assistant_mode;
        }
        if (Array.isArray(data.pdf_sources) && data.pdf_sources.length) {
          pdfSources = data.pdf_sources;
          window.pdfChatPdfSources = data.pdf_sources;
        }
        if (Array.isArray(data.pdf_source_keys) && data.pdf_source_keys.length) {
          selectedPdfSourceKeys = data.pdf_source_keys.map(String);
          window.pdfChatPdfSourceKeys = selectedPdfSourceKeys.slice();
        }
        if (data.attachment_id !== undefined && data.attachment_id !== null) {
          attachmentId = parseInt(data.attachment_id, 10);
          if (isNaN(attachmentId)) attachmentId = null;
        } else if (data.assistant_mode === 'chatpdf') {
          attachmentId = null;
        }
        syncAttachmentIdFromSelectedKeys();
        activeSourceLabel = data.active_source_label || activeSourceLabel;
        updateActiveSourceLabelFromKeys();
        var activeMode = data.assistant_mode || pdfChatAssistantMode || (chatType === 'forums_index' ? 'forums_index' : (chatType === 'forum' ? 'forum' : 'publication'));
        setupWelcomeForMode(activeMode);
        container.innerHTML = '';
        if (data.messages && data.messages.length) {
          data.messages.forEach(function (m) {
            appendMessage(m.role, m.content, false);
          });
        } else {
          setWelcomeVisible(true);
        }
      })
      .catch(function (err) {
        container.innerHTML = '';
        showError((err && err.message) ? err.message : 'Could not start chat. Please try again.');
      });
  }

  function sendMessageWithText(text) {
    var input = document.getElementById('pdf-chat-input');
    if (input) input.value = text;
    sendMessage();
  }

  function sendMessage() {
    var input = document.getElementById('pdf-chat-input');
    var text = (input && input.value) ? input.value.trim() : '';
    if (!text || isStreaming) return;
    if (!sessionId) {
      showError('Session not ready. Please wait or close and try again.');
      return;
    }
    if (chatType === 'forum' && !forumId) {
      showError('Forum context missing. Please reload and try again.');
      return;
    }
    if (chatType === 'forums_index' && (!forumsIndexForumIds || !forumsIndexForumIds.length)) {
      showError('No discussions available on this page.');
      return;
    }
    if (chatType === 'publication' && !publicationId) {
      showError('Resource context missing. Please reload and try again.');
      return;
    }

    isStreaming = true;
    input.value = '';
    var sendBtn = document.getElementById('pdf-chat-send');
    if (sendBtn) sendBtn.disabled = true;

    appendMessage('user', text, false);
    var contentEl = appendMessage('assistant', '', true);

    var url = '{{ url("ai/pdf-chat/message") }}';
    var body = chatType === 'forums_index'
      ? {
          assistant_mode: 'forums_index',
          forum_ids: forumsIndexForumIds,
          session_id: sessionId,
          message: text,
          stream: true
        }
      : (chatType === 'forum'
        ? {
            forum_id: forumId,
            session_id: sessionId,
            message: text,
            stream: true
          }
        : {
            publication_id: publicationId,
            session_id: sessionId,
            attachment_id: attachmentId || null,
            pdf_source_keys: selectedPdfSourceKeys.length ? selectedPdfSourceKeys : defaultSelectedPdfSourceKeys(),
            assistant_mode: pdfChatAssistantMode || 'auto',
            message: text,
            stream: true
          });

    fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getToken(),
        'Accept': 'text/plain'
      },
      body: JSON.stringify(body)
    })
      .then(function (response) {
        if (!response.ok) {
          return response.json().catch(function () { return {}; }).then(function (data) {
            throw new Error((data && data.error) ? data.error : 'Request failed');
          });
        }
        var reader = response.body.getReader();
        var decoder = new TextDecoder();
        var full = '';
        function read() {
          return reader.read().then(function (result) {
            if (result.done) return;
            var chunk = decoder.decode(result.value, { stream: true });
            full += chunk;
            setStreamingContent(contentEl, full);
            return read();
          });
        }
        return read();
      })
      .then(function () {
        var msg = document.querySelector('.pdf-chat-msg[data-streaming="1"]');
        if (msg) {
          msg.removeAttribute('data-streaming');
          var contentEl = msg.querySelector('.content');
          if (contentEl) finalizeStreamingMessage(contentEl);
        }
      })
      .catch(function (err) {
        var msg = (err && err.message) ? err.message : 'Sorry, something went wrong. Please try again.';
        setStreamingContent(contentEl, msg);
      })
      .finally(function () {
        isStreaming = false;
        if (sendBtn) sendBtn.disabled = false;
      });
  }

  document.addEventListener('DOMContentLoaded', function () {
    var sendBtn = document.getElementById('pdf-chat-send');
    var input = document.getElementById('pdf-chat-input');
    var exportAllPdf = document.getElementById('pdf-chat-export-all-pdf');
    var exportAllWord = document.getElementById('pdf-chat-export-all-word');
    var shareAllBtn = document.getElementById('pdf-chat-share-all');
    if (exportAllPdf) exportAllPdf.addEventListener('click', function() { exportAll('pdf'); });
    if (exportAllWord) exportAllWord.addEventListener('click', function() { exportAll('word'); });
    if (shareAllBtn) shareAllBtn.addEventListener('click', function(e) { e.stopPropagation(); shareConversation(); });
    if (sendBtn) sendBtn.addEventListener('click', sendMessage);
    var docSelect = document.getElementById('pdf-chat-doc-select');
    if (docSelect) {
      docSelect.addEventListener('change', function () {
        applyPdfDocumentSelection(this);
      });
    }
    if (input) {
      input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
          e.preventDefault();
          sendMessage();
        }
      });
      input.addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 140) + 'px';
      });
    }
    var modalEl = document.getElementById('pdf-chat-modal');
    if (modalEl) {
      modalEl.querySelectorAll('[data-dismiss="modal"], [data-bs-dismiss="modal"], .btn-close-placeholder').forEach(function(btn) {
        btn.addEventListener('click', function() {
          if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var m = bootstrap.Modal.getInstance(modalEl);
            if (m) m.hide();
            else new bootstrap.Modal(modalEl).hide();
          } else if (typeof $ !== 'undefined' && $.fn.modal) {
            $(modalEl).modal('hide');
          } else {
            modalEl.classList.remove('show');
            modalEl.style.display = 'none';
            document.body.classList.remove('modal-open');
            var back = document.getElementById('pdf-chat-modal-backdrop');
            if (back) back.remove();
          }
        });
      });
    }
  });
})();
</script>
