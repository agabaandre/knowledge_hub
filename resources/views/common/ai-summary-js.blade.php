<script type="text/javascript">
(function () {
  var resourceId = null;
  var isForum = 0;

  window.summarise = function (resource, isResourceForum) {
    resourceId = resource;
    isForum = isResourceForum || 0;
    document.getElementById('coppable').innerHTML = '';
    document.querySelector('#summarise-modal .copy').style.display = 'none';
    $('#summarise-modal').modal('show');
  };

  function getToken() {
    var m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute('content') : '';
  }

  window.startAiSummary = function () {
    var contentEl = document.getElementById('coppable');
    var goBtn = document.getElementById('ai-summary-go-btn');
    var copyBtn = document.querySelector('#summarise-modal .copy');

    if (!resourceId) return;
    contentEl.innerHTML = '<p class="ai-streaming-placeholder mb-0"><span class="spinner-border spinner-border-sm mr-2" role="status"></span>Analyzing and generating summary...</p>';
    copyBtn.style.display = 'none';
    if (goBtn) {
      goBtn.disabled = true;
    }

    var lang = document.querySelector('#summarise-modal .language');
    var promptInput = document.getElementById('prompt');
    var langVal = lang ? lang.value : 'English';
    var promptVal = promptInput ? promptInput.value.trim() : '';

    var url = '{{ url("ai/summarise-stream") }}';
    var body = JSON.stringify({
      resource_id: resourceId,
      type: isForum,
      language: langVal,
      prompt: promptVal || null
    });

    fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getToken(),
        'Accept': 'text/html'
      },
      body: body
    })
      .then(function (response) {
        if (!response.ok) throw new Error('Request failed');
        var reader = response.body.getReader();
        var decoder = new TextDecoder();
        var accumulated = '';
        function read() {
          return reader.read().then(function (result) {
            if (result.done) {
              if (accumulated) {
                contentEl.innerHTML = accumulated;
              }
              copyBtn.style.display = 'inline-block';
              if (goBtn) goBtn.disabled = false;
              return;
            }
            var chunk = decoder.decode(result.value, { stream: true });
            accumulated += chunk;
            contentEl.innerHTML = accumulated;
            contentEl.scrollTop = contentEl.scrollHeight;
            return read();
          });
        }
        return read();
      })
      .catch(function (err) {
        contentEl.innerHTML = '<div class="alert alert-danger">Could not generate summary. Please try again.</div>';
        if (goBtn) goBtn.disabled = false;
      });
  };

  window.copyToClipboard = function () {
    var content = document.getElementById('coppable');
    var text = content ? content.innerText : '';
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text).catch(function (e) { console.error(e); });
    }
  };
})();
</script>
