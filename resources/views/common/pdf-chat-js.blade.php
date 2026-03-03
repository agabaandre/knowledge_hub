<script>
(function () {
  var publicationId = typeof pdfChatPublicationId !== 'undefined' ? pdfChatPublicationId : null;
  var sessionId = null;
  var sourceId = null;
  var isStreaming = false;

  window.openPdfChat = function (pubId) {
    publicationId = pubId;
    sessionId = null;
    sourceId = null;
    $('#pdf-chat-modal').modal('show');
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
    el.innerHTML = '<div class="role-label">' + escapeHtml(label) + '</div><div class="content">' + (isStreamingPlaceholder ? '' : escapeHtml(content)) + '</div>';
    if (isStreamingPlaceholder) {
      el.dataset.streaming = '1';
    }
    messagesEl().appendChild(el);
    messagesEl().scrollTop = messagesEl().scrollHeight;
    return el.querySelector('.content');
  }

  function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  function setStreamingContent(contentEl, text) {
    contentEl.textContent = text;
    messagesEl().scrollTop = messagesEl().scrollHeight;
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
      body: JSON.stringify({ publication_id: publicationId })
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
        if (msg) msg.removeAttribute('data-streaming');
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
    if (sendBtn) sendBtn.addEventListener('click', sendMessage);
    if (input) {
      input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
          e.preventDefault();
          sendMessage();
        }
      });
    }
  });
})();
</script>
