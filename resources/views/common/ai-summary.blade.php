<style>
#summarise-modal .modal-dialog {
  max-width: 100%;
  width: 100%;
  height: 100vh;
  margin: 0;
}
#summarise-modal .modal-content {
  height: 100vh;
  border-radius: 0;
  display: flex;
  flex-direction: column;
}
#summarise-modal .modal-header {
  flex-shrink: 0;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid #dee2e6;
  background: #fff;
}
#summarise-modal .modal-body {
  flex: 1;
  overflow-y: auto;
  padding: 1.25rem;
  background: #f8f9fa;
  display: flex;
  flex-direction: column;
}
#summarise-modal .ai-summary-options {
  flex-shrink: 0;
  padding: 1rem 0;
  border-bottom: 1px solid #dee2e6;
  margin-bottom: 1rem;
  background: #fff;
  border-radius: 8px;
  padding: 1rem 1.25rem;
}
#summarise-modal .ai-content-wrap {
  flex: 1;
  min-height: 200px;
  background: #fff;
  border-radius: 8px;
  border: 1px solid #dee2e6;
  padding: 1.25rem;
  overflow-y: auto;
}
#summarise-modal .ai-content-wrap .ai-content {
  line-height: 1.6;
  color: #333;
}
#summarise-modal .ai-content-wrap .ai-content h3 { color: #0d9488; font-size: 1.15rem; margin-top: 1rem; margin-bottom: 0.5rem; }
#summarise-modal .ai-content-wrap .ai-content strong { color: #0d9488; }
#summarise-modal .modal-footer {
  flex-shrink: 0;
  padding: 1rem 1.25rem;
  border-top: 1px solid #dee2e6;
  background: #fff;
}
#summarise-modal .ai-streaming-placeholder {
  color: #6c757d;
  font-style: italic;
}
</style>
<div class="modal fade" id="summarise-modal" tabindex="-1" role="dialog" aria-labelledby="summarise-modal-label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="summarise-modal-label">
          <i class="fa-solid fa-microchip"></i> AI Processing (Summarizer)
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="ai-summary-options">
          @php $user = current_user(); @endphp
          <div class="row align-items-end">
            <div class="col-md-4 form-group mb-0">
              <label class="mb-1">Language</label>
              <select class="form-control form-control-sm language">
                <option {{ ($user && $user->langauge == 'en') ? 'selected' : '' }}>English</option>
                <option {{ ($user && $user->langauge == 'ar') ? 'selected' : '' }}>Arabic</option>
                <option {{ ($user && $user->langauge == 'fr') ? 'selected' : '' }}>French</option>
                <option {{ ($user && $user->langauge == 'pt') ? 'selected' : '' }}>Portuguese</option>
                <option {{ ($user && $user->langauge == 'es') ? 'selected' : '' }}>Spanish</option>
                <option {{ ($user && $user->langauge == 'sw') ? 'selected' : '' }}>Swahili</option>
              </select>
            </div>
            <div class="col-md-6 form-group mb-0">
              <label class="mb-1">Custom prompt (optional)</label>
              <input type="text" name="prompt" id="prompt" placeholder="E.g. Make a 1 page summary" class="form-control form-control-sm">
            </div>
            <div class="col-md-2 form-group mb-0">
              <button class="btn btn-primary btn-sm w-100" type="button" onclick="startAiSummary()" id="ai-summary-go-btn">
                <i class="fa-solid fa-robot"></i> Process
              </button>
            </div>
          </div>
        </div>
        <div class="ai-content-wrap">
          <div class="ai-content" id="coppable"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary btn-sm copy" style="display: none;" type="button" onclick="copyToClipboard()">
          <i class="fa fa-copy"></i> Copy
        </button>
        <button class="btn btn-secondary btn-sm" data-dismiss="modal" type="button">Close</button>
      </div>
    </div>
  </div>
</div>

@section('scripts')
  @include('common.ai-summary-js')
@endsection
