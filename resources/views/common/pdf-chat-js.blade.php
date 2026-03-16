<script>
(function () {
  var publicationId = typeof pdfChatPublicationId !== 'undefined' ? pdfChatPublicationId : null;
  var attachmentId = typeof pdfChatAttachmentId !== 'undefined' ? pdfChatAttachmentId : null;
  var sessionId = null;
  var sourceId = null;
  var isStreaming = false;

  window.openPdfChat = function (pubId, attId, docTitle) {
    publicationId = pubId;
    attachmentId = attId === undefined ? null : attId;
    sessionId = null;
    sourceId = null;
    var titleEl = document.getElementById('pdf-chat-doc-title');
    if (titleEl) titleEl.textContent = (typeof docTitle === 'string' && docTitle) ? docTitle : (typeof pdfChatDocumentTitle !== 'undefined' ? pdfChatDocumentTitle : 'Document');
    var modalEl = document.getElementById('pdf-chat-modal');
    if (!modalEl) {
      loadPdfChatSession();
      return;
    }
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
      bootstrap.Modal.getOrCreateInstance(modalEl).show();
    } else if (typeof $ !== 'undefined' && $.fn.modal) {
      $(modalEl).modal('show');
    } else {
      modalEl.classList.add('show');
      modalEl.style.display = 'block';
      document.body.classList.add('modal-open');
      var backdrop = document.createElement('div');
      backdrop.className = 'modal-backdrop fade show';
      backdrop.id = 'pdf-chat-modal-backdrop';
      document.body.appendChild(backdrop);
    }
    loadPdfChatSession();
  };

  function getToken() {
    var m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute('content') : '';
  }

  function messagesEl() {
    return document.getElementById('pdf-chat-messages');
  }

  function appendMessage(role, content, isStreamingPlaceholder) {
    var el = document.createElement('div');
    el.className = 'pdf-chat-msg ' + role;
    var label = role === 'user' ? 'You' : 'Assistant';
    var contentHtml = isStreamingPlaceholder ? '' : (role === 'assistant' ? markdownToHtml(content) : escapeHtml(content));
    var actionsHtml = (role === 'assistant' && content && !isStreamingPlaceholder)
      ? '<div class="pdf-chat-msg-actions"><button type="button" class="btn btn-outline-secondary btn-sm pdf-chat-export-pdf" title="Download as PDF"><i class="fa fa-file-pdf"></i> PDF</button><button type="button" class="btn btn-outline-secondary btn-sm pdf-chat-export-word" title="Export as Word"><i class="fa fa-file-word"></i> Word</button></div>'
      : '';
    el.innerHTML = '<div class="role-label">' + escapeHtml(label) + '</div><div class="content">' + contentHtml + '</div>' + actionsHtml;
    if (isStreamingPlaceholder) {
      el.dataset.streaming = '1';
    }
    messagesEl().appendChild(el);
    messagesEl().scrollTop = messagesEl().scrollHeight;
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

  function setStreamingContent(contentEl, text) {
    contentEl.textContent = text;
    messagesEl().scrollTop = messagesEl().scrollHeight;
  }

  function finalizeStreamingMessage(contentEl) {
    var text = contentEl.textContent || '';
    contentEl.innerHTML = markdownToHtml(text);
    var msgDiv = contentEl.closest('.pdf-chat-msg');
    if (msgDiv && text) {
      var btn = '<div class="pdf-chat-msg-actions"><button type="button" class="btn btn-outline-secondary btn-sm pdf-chat-export-pdf" title="Download as PDF"><i class="fa fa-file-pdf"></i> PDF</button><button type="button" class="btn btn-outline-secondary btn-sm pdf-chat-export-word" title="Export as Word"><i class="fa fa-file-word"></i> Word</button></div>';
      var wrap = document.createElement('div');
      wrap.innerHTML = btn;
      msgDiv.appendChild(wrap.firstChild);
      var contentForExport = text;
      msgDiv.querySelector('.pdf-chat-export-pdf').addEventListener('click', function() { exportContent(contentForExport, 'pdf'); });
      msgDiv.querySelector('.pdf-chat-export-word').addEventListener('click', function() { exportContent(contentForExport, 'word'); });
    }
  }

  function getDocumentTitle() {
    var el = document.getElementById('pdf-chat-doc-title');
    return (el && el.textContent) ? el.textContent.trim() : 'Chat export';
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
    var el = document.createElement('div');
    el.className = 'alert alert-danger pdf-chat-msg';
    el.textContent = msg;
    messagesEl().appendChild(el);
    messagesEl().scrollTop = messagesEl().scrollHeight;
  }

  function loadPdfChatSession() {
    var container = messagesEl();
    container.innerHTML = '<div class="text-center py-4"><span class="spinner-border text-primary"></span> Loading...</div>';

    fetch('{{ url("ai/pdf-chat/session") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getToken(),
        'Accept': 'application/json'
      },
      body: JSON.stringify({ publication_id: publicationId, attachment_id: attachmentId || null })
    })
      .then(function (r) {
        if (!r.ok) throw new Error('Session failed');
        return r.json();
      })
      .then(function (data) {
        if (data.error) {
          container.innerHTML = '';
          showError(data.error);
          return;
        }
        sessionId = data.session_id;
        sourceId = data.source_id;
        container.innerHTML = '';
        if (data.messages && data.messages.length) {
          data.messages.forEach(function (m) {
            appendMessage(m.role, m.content, false);
          });
        } else {
          appendMessage('assistant', 'Ask anything about this PDF. You can request a summary, ask about specific sections, or pose questions.', false);
        }
      })
      .catch(function (err) {
        container.innerHTML = '';
        showError('Could not start chat. Please try again.');
      });
  }

  function sendMessage() {
    var input = document.getElementById('pdf-chat-input');
    var text = (input && input.value) ? input.value.trim() : '';
    if (!text || isStreaming) return;
    if (!sessionId && !sourceId) {
      showError('Session not ready. Please wait or close and try again.');
      return;
    }

    isStreaming = true;
    input.value = '';
    var sendBtn = document.getElementById('pdf-chat-send');
    if (sendBtn) sendBtn.disabled = true;

    appendMessage('user', text, false);
    var contentEl = appendMessage('assistant', '', true);

    var url = '{{ url("ai/pdf-chat/message") }}';
    var body = {
      publication_id: publicationId,
      session_id: sessionId,
      attachment_id: attachmentId || null,
      message: text,
      stream: true
    };

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
        if (!response.ok) throw new Error('Request failed');
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
      .catch(function () {
        setStreamingContent(contentEl, contentEl.textContent || 'Sorry, something went wrong. Please try again.');
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
    if (exportAllPdf) exportAllPdf.addEventListener('click', function() { exportAll('pdf'); });
    if (exportAllWord) exportAllWord.addEventListener('click', function() { exportAll('word'); });
    if (sendBtn) sendBtn.addEventListener('click', sendMessage);
    if (input) {
      input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
          e.preventDefault();
          sendMessage();
        }
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
