{{-- Full-screen Khub AI Assistant (ChatPDF for PDFs; GPT context for other resources). --}}
<style>
#pdf-chat-modal .modal-dialog {
  max-width: 100%;
  width: 100%;
  height: 100vh;
  margin: 0;
}
#pdf-chat-modal .modal-content {
  height: 100vh;
  border-radius: 0;
  display: flex;
  flex-direction: column;
}
#pdf-chat-modal .modal-header {
  flex-shrink: 0;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid #dee2e6;
  background: #fff;
}
#pdf-chat-modal .pdf-chat-doc-title {
  font-size: 0.95rem;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 0.5rem 0;
  line-height: 1.3;
}
#pdf-chat-modal .pdf-chat-export-toolbar {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
  margin-top: 0.5rem;
}
#pdf-chat-modal .pdf-chat-export-toolbar .btn {
  font-size: 0.8rem;
  padding: 0.35rem 0.6rem;
}
.pdf-chat-msg.assistant .pdf-chat-msg-actions {
  margin-top: 0.5rem;
  display: flex;
  gap: 0.35rem;
  flex-wrap: wrap;
  align-items: center;
}
.pdf-chat-msg.assistant .pdf-chat-msg-actions .btn {
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
}
.pdf-chat-share-dropdown {
  position: absolute;
  z-index: 1060;
  background: #fff;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  padding: 0.35rem 0;
  min-width: 180px;
}
.pdf-chat-share-dropdown a, .pdf-chat-share-dropdown button {
  display: block;
  width: 100%;
  text-align: left;
  padding: 0.4rem 0.75rem;
  border: none;
  background: none;
  color: #212529;
  font-size: 0.875rem;
  text-decoration: none;
  cursor: pointer;
}
.pdf-chat-share-dropdown a:hover, .pdf-chat-share-dropdown button:hover {
  background: #f8f9fa;
}
.pdf-chat-share-dropdown a i, .pdf-chat-share-dropdown button i {
  margin-right: 0.5rem;
  width: 1.1em;
}
.pdf-chat-msg-actions .dropdown-wrap { position: relative; display: inline-block; }
.pdf-chat-msg.assistant .content {
  white-space: normal;
}
.pdf-chat-msg.assistant .content h3 { font-size: 1rem; margin: 1rem 0 0.5rem; font-weight: 600; }
.pdf-chat-msg.assistant .content h4 { font-size: 0.95rem; margin: 0.85rem 0 0.4rem; font-weight: 600; }
.pdf-chat-msg.assistant .content p { margin: 0.5rem 0; }
.pdf-chat-msg.assistant .content ul { margin: 0.5rem 0; padding-left: 1.25rem; }
.pdf-chat-msg.assistant .content li { margin: 0.25rem 0; }
.pdf-chat-msg.assistant .content hr { border: 0; border-top: 1px solid #dee2e6; margin: 0.75rem 0; }
#pdf-chat-modal .modal-body {
  flex: 1;
  overflow-y: auto;
  padding: 1.25rem;
  background: #f8f9fa;
}
#pdf-chat-modal .modal-footer {
  flex-shrink: 0;
  padding: 1rem 1.25rem;
  border-top: 1px solid #dee2e6;
  background: #fff;
}
.pdf-chat-messages {
  min-height: 200px;
}
.pdf-chat-msg {
  max-width: 85%;
  margin-bottom: 1rem;
  padding: 0.75rem 1rem;
  border-radius: 12px;
  line-height: 1.5;
}
.pdf-chat-msg.user {
  margin-left: auto;
  background: var(--theme-color-primary, #006239);
  color: #fff;
}
.pdf-chat-msg.assistant {
  margin-right: auto;
  background: #fff;
  border: 1px solid #dee2e6;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
.pdf-chat-msg.assistant .content {
  word-break: break-word;
}
.pdf-chat-msg .role-label {
  font-size: 0.75rem;
  opacity: 0.85;
  margin-bottom: 0.25rem;
}
.pdf-chat-typing {
  display: inline-block;
  padding: 0.5rem 0.75rem;
  border-radius: 12px;
}
.pdf-chat-input-wrap {
  display: flex;
  gap: 0.5rem;
  align-items: flex-end;
}
.pdf-chat-input-wrap textarea {
  flex: 1;
  min-height: 44px;
  max-height: 120px;
  resize: vertical;
}
.pdf-chat-send-btn:disabled {
  cursor: not-allowed;
}
#pdf-chat-modal .pdf-chat-send-btn.btn-primary,
#pdf-chat-modal button.pdf-chat-send-btn {
  background-color: var(--theme-color-primary, #119A48) !important;
  border-color: var(--theme-color-primary, #119A48) !important;
  color: #fff !important;
}
#pdf-chat-modal .pdf-chat-send-btn.btn-primary:hover:not(:disabled),
#pdf-chat-modal button.pdf-chat-send-btn:hover:not(:disabled) {
  background-color: color-mix(in srgb, var(--theme-color-primary, #119A48) 88%, #000) !important;
  border-color: color-mix(in srgb, var(--theme-color-primary, #119A48) 88%, #000) !important;
  color: #fff !important;
}
#pdf-chat-modal .pdf-chat-send-btn:disabled {
  opacity: 0.65;
}
</style>
<div class="modal fade" id="pdf-chat-modal" tabindex="-1" role="dialog" aria-labelledby="pdf-chat-modal-label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="flex-grow-1">
          <h5 class="modal-title mb-0" id="pdf-chat-modal-label">
            <i class="fa-solid fa-microchip"></i> Khub AI Assistant
          </h5>
          <p class="pdf-chat-doc-title mb-0" id="pdf-chat-doc-title" aria-hidden="true">—</p>
          <div class="pdf-chat-export-toolbar">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="pdf-chat-export-all-pdf" title="Export full conversation as PDF">
              <i class="fa fa-file-pdf"></i> Export all (PDF)
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="pdf-chat-export-all-word" title="Export full conversation as Word">
              <i class="fa fa-file-word"></i> Export all (Word)
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="pdf-chat-share-all" title="Share via social or copy link">
              <i class="fa fa-share-alt"></i> Share
            </button>
          </div>
        </div>
        <button type="button" class="close btn-close-placeholder" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="pdf-chat-messages" id="pdf-chat-messages"></div>
      </div>
      <div class="modal-footer">
        <div class="pdf-chat-input-wrap w-100">
          <textarea class="form-control" id="pdf-chat-input" placeholder="Ask anything about this resource..." rows="1"></textarea>
          <button type="button" class="btn btn-primary pdf-chat-send-btn" id="pdf-chat-send">
            <i class="fa fa-paper-plane"></i> Send
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
