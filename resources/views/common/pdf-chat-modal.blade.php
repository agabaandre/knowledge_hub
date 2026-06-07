{{-- Full-screen Khub AI (ChatPDF for PDFs; GPT context for other resources). --}}
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
  border: none;
  background: #f4f6f8;
}
#pdf-chat-modal .khub-ai-header {
  flex-shrink: 0;
  padding: 0;
  border-bottom: 1px solid #e2e8f0;
  background: #fff;
}
#pdf-chat-modal .khub-ai-header-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 0.85rem 1.25rem;
  border-bottom: 1px solid #eef2f6;
}
#pdf-chat-modal .khub-ai-brand {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  min-width: 0;
}
#pdf-chat-modal .khub-ai-brand-icon {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, var(--theme-color-primary, #006239) 0%, color-mix(in srgb, var(--theme-color-primary, #006239) 70%, #0ea5e9) 100%);
  color: #fff;
  font-size: 1.1rem;
  flex-shrink: 0;
  box-shadow: 0 4px 14px color-mix(in srgb, var(--theme-color-primary, #006239) 25%, transparent);
}
#pdf-chat-modal .khub-ai-brand-text h5 {
  margin: 0;
  font-size: 1.05rem;
  font-weight: 700;
  color: #0f172a;
  line-height: 1.2;
}
#pdf-chat-modal .khub-ai-brand-text p {
  margin: 0.1rem 0 0;
  font-size: 0.78rem;
  color: #64748b;
}
#pdf-chat-modal .khub-ai-header-actions {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  flex-shrink: 0;
}
#pdf-chat-modal .khub-ai-mode-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.72rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  padding: 0.3rem 0.65rem;
  border-radius: 999px;
  background: #ecfdf5;
  color: #047857;
  border: 1px solid #a7f3d0;
}
#pdf-chat-modal .khub-ai-mode-badge.forum {
  background: #eff6ff;
  color: #1d4ed8;
  border-color: #bfdbfe;
}
#pdf-chat-modal .khub-ai-mode-badge.publication {
  background: #f5f3ff;
  color: #6d28d9;
  border-color: #ddd6fe;
}
#pdf-chat-modal .khub-ai-close {
  width: 2.25rem;
  height: 2.25rem;
  border: none;
  border-radius: 10px;
  background: #f1f5f9;
  color: #475569;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.15s ease, color 0.15s ease;
}
#pdf-chat-modal .khub-ai-close:hover {
  background: #e2e8f0;
  color: #0f172a;
}
#pdf-chat-modal .khub-ai-doc-panel {
  display: flex;
  align-items: flex-start;
  gap: 0.85rem;
  padding: 0.9rem 1.25rem 1rem;
  flex-wrap: wrap;
}
#pdf-chat-modal .khub-ai-doc-icon {
  width: 2.75rem;
  height: 2.75rem;
  border-radius: 10px;
  background: #fff7ed;
  color: #c2410c;
  border: 1px solid #fed7aa;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
  flex-shrink: 0;
}
#pdf-chat-modal .khub-ai-doc-icon.is-forum {
  background: #eff6ff;
  color: #2563eb;
  border-color: #bfdbfe;
}
#pdf-chat-modal .khub-ai-doc-icon.is-resource {
  background: #f5f3ff;
  color: #7c3aed;
  border-color: #ddd6fe;
}
#pdf-chat-modal .khub-ai-doc-meta {
  flex: 1;
  min-width: 200px;
}
#pdf-chat-modal .khub-ai-doc-title {
  font-size: 0.95rem;
  font-weight: 600;
  color: #0f172a;
  margin: 0;
  line-height: 1.45;
}
#pdf-chat-modal .khub-ai-doc-hint {
  margin: 0.25rem 0 0;
  font-size: 0.8rem;
  color: #64748b;
  line-height: 1.4;
}
#pdf-chat-modal .khub-ai-toolbar {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  flex-wrap: wrap;
  margin-left: auto;
}
#pdf-chat-modal .khub-ai-toolbar .btn {
  font-size: 0.78rem;
  padding: 0.38rem 0.7rem;
  border-radius: 8px;
  font-weight: 500;
  border-color: #cbd5e1;
  color: #334155;
  background: #fff;
}
#pdf-chat-modal .khub-ai-toolbar .btn:hover {
  background: #f8fafc;
  border-color: #94a3b8;
  color: #0f172a;
}
#pdf-chat-modal .modal-body {
  flex: 1;
  overflow: hidden;
  padding: 0;
  display: flex;
  flex-direction: column;
  background: #f4f6f8;
}
#pdf-chat-modal .khub-ai-chat-scroll {
  flex: 1;
  overflow-y: auto;
  padding: 1.25rem 1.25rem 0.5rem;
}
#pdf-chat-modal .khub-ai-welcome {
  max-width: 720px;
  margin: 0 auto 1.25rem;
  padding: 1.25rem 1.35rem;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}
#pdf-chat-modal .khub-ai-welcome.is-hidden {
  display: none;
}
#pdf-chat-modal .khub-ai-welcome h6 {
  margin: 0 0 0.35rem;
  font-size: 0.95rem;
  font-weight: 700;
  color: #0f172a;
}
#pdf-chat-modal .khub-ai-welcome p {
  margin: 0;
  font-size: 0.875rem;
  color: #475569;
  line-height: 1.55;
}
#pdf-chat-modal .khub-ai-welcome ul {
  margin: 0.75rem 0 0;
  padding-left: 1.1rem;
  font-size: 0.82rem;
  color: #64748b;
}
#pdf-chat-modal .khub-ai-welcome li {
  margin: 0.2rem 0;
}
#pdf-chat-modal .pdf-chat-messages {
  max-width: 860px;
  margin: 0 auto;
  min-height: 120px;
}
#pdf-chat-modal .pdf-chat-msg-row {
  display: flex;
  gap: 0.65rem;
  margin-bottom: 1.1rem;
  align-items: flex-start;
}
#pdf-chat-modal .pdf-chat-msg-row.user {
  flex-direction: row-reverse;
}
#pdf-chat-modal .pdf-chat-avatar {
  width: 2rem;
  height: 2rem;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
  flex-shrink: 0;
}
#pdf-chat-modal .pdf-chat-msg-row.assistant .pdf-chat-avatar {
  background: linear-gradient(135deg, var(--theme-color-primary, #006239), color-mix(in srgb, var(--theme-color-primary, #006239) 75%, #0ea5e9));
  color: #fff;
}
#pdf-chat-modal .pdf-chat-msg-row.user .pdf-chat-avatar {
  background: #e2e8f0;
  color: #475569;
}
#pdf-chat-modal .pdf-chat-msg {
  max-width: min(78%, 640px);
  padding: 0;
  margin: 0;
  border-radius: 0;
  background: transparent;
  box-shadow: none;
}
#pdf-chat-modal .pdf-chat-msg-bubble {
  padding: 0.8rem 1rem;
  border-radius: 14px;
  line-height: 1.55;
  font-size: 0.9rem;
}
#pdf-chat-modal .pdf-chat-msg-row.user .pdf-chat-msg-bubble {
  background: var(--theme-color-primary, #006239);
  color: #fff;
  border-bottom-right-radius: 4px;
}
#pdf-chat-modal .pdf-chat-msg-row.assistant .pdf-chat-msg-bubble {
  background: #fff;
  border: 1px solid #e2e8f0;
  color: #1e293b;
  border-bottom-left-radius: 4px;
  box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
}
#pdf-chat-modal .pdf-chat-msg .role-label {
  font-size: 0.72rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  margin-bottom: 0.35rem;
  opacity: 0.75;
}
#pdf-chat-modal .pdf-chat-msg-row.user .role-label {
  text-align: right;
  color: color-mix(in srgb, #fff 80%, transparent);
}
#pdf-chat-modal .pdf-chat-msg-row.assistant .role-label {
  color: #64748b;
}
#pdf-chat-modal .pdf-chat-msg.assistant .content {
  white-space: normal;
  word-break: break-word;
}
#pdf-chat-modal .pdf-chat-msg.assistant .content h3 { font-size: 1rem; margin: 1rem 0 0.5rem; font-weight: 600; color: var(--theme-color-primary, #006239); }
#pdf-chat-modal .pdf-chat-msg.assistant .content h4 { font-size: 0.95rem; margin: 0.85rem 0 0.4rem; font-weight: 600; color: var(--theme-color-primary, #006239); }
#pdf-chat-modal .pdf-chat-msg.assistant .content p { margin: 0.5rem 0; }
#pdf-chat-modal .pdf-chat-msg.assistant .content ul { margin: 0.5rem 0; padding-left: 1.25rem; }
#pdf-chat-modal .pdf-chat-msg.assistant .content li { margin: 0.25rem 0; }
#pdf-chat-modal .pdf-chat-msg.assistant .content hr { border: 0; border-top: 1px solid #e2e8f0; margin: 0.75rem 0; }
#pdf-chat-modal .pdf-chat-msg.assistant .pdf-chat-msg-actions {
  margin-top: 0.55rem;
  display: flex;
  gap: 0.35rem;
  flex-wrap: wrap;
  align-items: center;
}
#pdf-chat-modal .pdf-chat-msg.assistant .pdf-chat-msg-actions .btn {
  font-size: 0.72rem;
  padding: 0.22rem 0.5rem;
  border-radius: 6px;
}
#pdf-chat-modal .pdf-chat-loading {
  max-width: 720px;
  margin: 2rem auto;
  text-align: center;
  color: #64748b;
}
#pdf-chat-modal .pdf-chat-loading .spinner-border {
  width: 2rem;
  height: 2rem;
  color: var(--theme-color-primary, #006239) !important;
}
#pdf-chat-modal .pdf-chat-loading p {
  margin: 0.75rem 0 0;
  font-size: 0.875rem;
}
#pdf-chat-modal .pdf-chat-error {
  max-width: 720px;
  margin: 0 auto 1rem;
  padding: 0.85rem 1rem;
  border-radius: 12px;
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: #b91c1c;
  font-size: 0.875rem;
  display: flex;
  align-items: flex-start;
  gap: 0.6rem;
}
#pdf-chat-modal .pdf-chat-error i {
  margin-top: 0.1rem;
}
#pdf-chat-modal .modal-footer.khub-ai-composer {
  flex-shrink: 0;
  padding: 0.85rem 1.25rem 1rem;
  border-top: 1px solid #e2e8f0;
  background: #fff;
  display: block;
}
#pdf-chat-modal .khub-ai-suggestions {
  max-width: 860px;
  margin: 0 auto 0.65rem;
  display: flex;
  flex-wrap: wrap;
  gap: 0.45rem;
}
#pdf-chat-modal .khub-ai-suggestions.is-hidden {
  display: none;
}
#pdf-chat-modal .khub-ai-suggestion {
  border: 1px solid #cbd5e1;
  background: #f8fafc;
  color: #334155;
  font-size: 0.78rem;
  padding: 0.35rem 0.7rem;
  border-radius: 999px;
  cursor: pointer;
  transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
}
#pdf-chat-modal .khub-ai-suggestion:hover {
  background: #fff;
  border-color: var(--theme-color-primary, #006239);
  color: var(--theme-color-primary, #006239);
}
#pdf-chat-modal .khub-ai-input-row {
  max-width: 860px;
  margin: 0 auto;
  display: flex;
  gap: 0.55rem;
  align-items: flex-end;
  background: #f8fafc;
  border: 1px solid #cbd5e1;
  border-radius: 14px;
  padding: 0.45rem 0.45rem 0.45rem 0.85rem;
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
#pdf-chat-modal .khub-ai-input-row:focus-within {
  border-color: var(--theme-color-primary, #006239);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--theme-color-primary, #006239) 15%, transparent);
  background: #fff;
}
#pdf-chat-modal .khub-ai-input-row textarea {
  flex: 1;
  min-height: 44px;
  max-height: 140px;
  resize: none;
  border: none;
  background: transparent;
  box-shadow: none;
  padding: 0.45rem 0;
  font-size: 0.9rem;
  line-height: 1.45;
}
#pdf-chat-modal .khub-ai-input-row textarea:focus {
  outline: none;
  box-shadow: none;
}
#pdf-chat-modal .pdf-chat-send-btn {
  border-radius: 10px !important;
  padding: 0.55rem 1rem !important;
  font-weight: 600;
  font-size: 0.85rem;
  white-space: nowrap;
}
#pdf-chat-modal .pdf-chat-send-btn:disabled {
  cursor: not-allowed;
  opacity: 0.65;
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
}
#pdf-chat-modal .khub-ai-disclaimer {
  max-width: 860px;
  margin: 0.55rem auto 0;
  font-size: 0.72rem;
  color: #94a3b8;
  text-align: center;
  line-height: 1.4;
}
.pdf-chat-share-dropdown {
  position: absolute;
  z-index: 1060;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
  padding: 0.35rem 0;
  min-width: 190px;
}
.pdf-chat-share-dropdown a, .pdf-chat-share-dropdown button {
  display: block;
  width: 100%;
  text-align: left;
  padding: 0.45rem 0.85rem;
  border: none;
  background: none;
  color: #334155;
  font-size: 0.85rem;
  text-decoration: none;
  cursor: pointer;
}
.pdf-chat-share-dropdown a:hover, .pdf-chat-share-dropdown button:hover {
  background: #f8fafc;
}
.pdf-chat-share-dropdown a i, .pdf-chat-share-dropdown button i {
  margin-right: 0.5rem;
  width: 1.1em;
  color: #64748b;
}
.pdf-chat-msg-actions .dropdown-wrap { position: relative; display: inline-block; }
@media (max-width: 767.98px) {
  #pdf-chat-modal .khub-ai-doc-panel {
    flex-direction: column;
  }
  #pdf-chat-modal .khub-ai-toolbar {
    margin-left: 0;
    width: 100%;
  }
  #pdf-chat-modal .pdf-chat-msg {
    max-width: 92%;
  }
  #pdf-chat-modal .khub-ai-header-top {
    padding: 0.75rem 1rem;
  }
  #pdf-chat-modal .khub-ai-doc-panel {
    padding: 0.75rem 1rem 0.9rem;
  }
  #pdf-chat-modal .modal-footer.khub-ai-composer,
  #pdf-chat-modal .khub-ai-chat-scroll {
    padding-left: 1rem;
    padding-right: 1rem;
  }
}
</style>
<div class="modal fade" id="pdf-chat-modal" tabindex="-1" role="dialog" aria-labelledby="pdf-chat-modal-label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header khub-ai-header">
        <div class="w-100">
          <div class="khub-ai-header-top">
            <div class="khub-ai-brand">
              <span class="khub-ai-brand-icon" aria-hidden="true"><i class="fa-solid fa-microchip"></i></span>
              <div class="khub-ai-brand-text">
                <h5 id="pdf-chat-modal-label">Khub AI</h5>
                <p>Intelligent assistant for health knowledge resources</p>
              </div>
            </div>
            <div class="khub-ai-header-actions">
              <span class="khub-ai-mode-badge" id="pdf-chat-mode-badge" aria-live="polite">Document chat</span>
              <button type="button" class="khub-ai-close btn-close-placeholder" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                <i class="fa fa-times"></i>
              </button>
            </div>
          </div>
          <div class="khub-ai-doc-panel">
            <div class="khub-ai-doc-icon" id="pdf-chat-doc-icon" aria-hidden="true"><i class="fa fa-file-pdf"></i></div>
            <div class="khub-ai-doc-meta">
              <p class="khub-ai-doc-title" id="pdf-chat-doc-title">—</p>
              <p class="khub-ai-doc-hint" id="pdf-chat-doc-hint">Ask questions grounded in this resource. Export or share responses anytime.</p>
            </div>
            <div class="khub-ai-toolbar">
              <button type="button" class="btn btn-outline-secondary btn-sm" id="pdf-chat-export-all-pdf" title="Export full conversation as PDF">
                <i class="fa fa-file-pdf"></i> Export PDF
              </button>
              <button type="button" class="btn btn-outline-secondary btn-sm" id="pdf-chat-export-all-word" title="Export full conversation as Word">
                <i class="fa fa-file-word"></i> Export Word
              </button>
              <button type="button" class="btn btn-outline-secondary btn-sm" id="pdf-chat-share-all" title="Share via social or copy link">
                <i class="fa fa-share-alt"></i> Share
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body">
        <div class="khub-ai-chat-scroll" id="pdf-chat-scroll">
          <div class="khub-ai-welcome is-hidden" id="pdf-chat-welcome" aria-hidden="true">
            <h6>How Khub AI can help</h6>
            <p id="pdf-chat-welcome-text">Start with a question below or choose a suggested prompt.</p>
            <ul id="pdf-chat-welcome-tips">
              <li>Answers are based on the resource content available to the assistant.</li>
              <li>Use follow-up questions to explore topics in more depth.</li>
              <li>Export individual replies or the full conversation when needed.</li>
            </ul>
          </div>
          <div class="pdf-chat-messages" id="pdf-chat-messages"></div>
        </div>
      </div>
      <div class="modal-footer khub-ai-composer">
        <div class="khub-ai-suggestions is-hidden" id="pdf-chat-suggestions" aria-label="Suggested prompts"></div>
        <div class="khub-ai-input-row">
          <textarea class="form-control" id="pdf-chat-input" placeholder="Ask anything about this resource…" rows="1" aria-label="Message Khub AI"></textarea>
          <button type="button" class="btn btn-primary pdf-chat-send-btn" id="pdf-chat-send">
            <i class="fa fa-paper-plane"></i> Send
          </button>
        </div>
        <p class="khub-ai-disclaimer">AI-generated responses may contain errors. Verify important information against the original document.</p>
      </div>
    </div>
  </div>
</div>
