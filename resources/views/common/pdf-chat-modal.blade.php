{{-- Full-screen PDF chat modal (ChatPDF). Include only on publication show when publication has PDF. --}}
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
  background: #0d6efd;
  color: #fff;
}
.pdf-chat-msg.assistant {
  margin-right: auto;
  background: #fff;
  border: 1px solid #dee2e6;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
.pdf-chat-msg.assistant .content {
  white-space: pre-wrap;
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
</style>
<div class="modal fade" id="pdf-chat-modal" tabindex="-1" role="dialog" aria-labelledby="pdf-chat-modal-label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pdf-chat-modal-label">
          <i class="fa-solid fa-microchip"></i> Chat with PDF
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="pdf-chat-messages" id="pdf-chat-messages"></div>
      </div>
      <div class="modal-footer">
        <div class="pdf-chat-input-wrap w-100">
          <textarea class="form-control" id="pdf-chat-input" placeholder="Ask anything about this PDF..." rows="1"></textarea>
          <button type="button" class="btn btn-primary pdf-chat-send-btn" id="pdf-chat-send">
            <i class="fa fa-paper-plane"></i> Send
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
