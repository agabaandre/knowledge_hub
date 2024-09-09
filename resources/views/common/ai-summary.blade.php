<style>
  .modal-fullscreen {
    width: 90%;
    min-height: 90vh !important;
    max-height: 90vh !important;
    margin: 5%;
    padding: 0;
    max-width: none;
    border-radius: 15px;
  }

  .modal-header {
    background-color: #343a40;
    color: white;
    padding: 20px;
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
  }

  .modal-header h2 {
    font-size: 24px;
    font-weight: bold;
  }

  .modal-body {
    padding: 30px;
    background-color: #f9f9f9;
  }

  .form-group label {
    font-weight: bold;
    font-size: 14px;
  }

  .form-control {
    padding: 10px 12px;
    font-size: 16px;
    border-radius: 10px;
  }

  .form-control.language {
    position: relative;
    padding-left: 40px;
  }

  .form-control[name="prompt"] {
    position: relative;
    padding-left: 40px;
  }

  /* Add icon inside the language select */
  .language-icon {
    position: absolute;
    left: 10px;
    top: 35px;
    font-size: 20px;
    color: #007bff;
  }

  /* Add icon inside the prompt input */
  .prompt-icon {
    position: absolute;
    left: 10px;
    top: 35px;
    font-size: 20px;
    color: #007bff;
  }

  /* Modal footer buttons */
  .modal-footer {
    background-color: #f1f1f1;
    padding: 15px;
    border-bottom-left-radius: 15px;
    border-bottom-right-radius: 15px;
  }

  .btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 16px;
    transition: background-color 0.3s;
  }

  .btn-success {
    background-color: #28a745;
    color: white;
  }

  .btn-success:hover {
    background-color: #218838;
  }

  .btn-danger {
    background-color: #dc3545;
    color: white;
  }

  .btn-danger:hover {
    background-color: #c82333;
  }

  .btn.copy {
    background-color: #007bff;
    color: white;
  }

  .btn.copy:hover {
    background-color: #0056b3;
  }

  /* Modal transition */
  .modal.fade .modal-dialog {
    transform: scale(0.9);
    transition: transform 0.3s ease-out;
  }

  .modal.show .modal-dialog {
    transform: scale(1);
  }
</style>

<!-- Extra Large modal example -->
<div class="modal" id="summarise-modal">
  <div class="modal-dialog modal-lg modal-fullscreen">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="myExtraLargeModalLabel">
          <i class="fa-solid fa-microchip"></i> AI Processing (Summarizer)
        </h2>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="ft-lg" aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        @php
      $user = current_user();
      @endphp

        <div class="row mb-10 d-flex justify-content-center">
          <!-- Language Selection -->
          <div class="form-group col-md-4 position-relative">
            <label for="language">Language</label>
            <i class="fa-solid fa-language language-icon"></i>
            <select class="form-control language" id="language">
              <option {{ ($user && 'en' == $user->langauge) ? 'selected' : '' }}>English</option>
              <option {{ ($user && 'ar' == $user->langauge) ? 'selected' : '' }}>Arabic</option>
              <option {{ ($user && 'fr' == $user->langauge) ? 'selected' : '' }}>French</option>
              <option {{ ($user && 'pt' == $user->langauge) ? 'selected' : '' }}>Portuguese</option>
              <option {{ ($user && 'es' == $user->langauge) ? 'selected' : '' }}>Spanish</option>
              <option {{ ($user && 'sw' == $user->langauge) ? 'selected' : '' }}>Swahili</option>
            </select>
          </div>

          <!-- Custom Prompt -->
          <div class="form-group col-md-8 position-relative">
            <label for="prompt">Custom Prompt</label>
            <i class="fa-solid fa-pencil-alt prompt-icon"></i>
            <input type="text" id="prompt" name="prompt" placeholder="E.g 'Make a 1 Page Summary'" class="form-control">
          </div>
        </div>

        <div class="row d-flex justify-content-center">
          <div class="ai-content" id="coppable" style="padding: 30px;"></div>
        </div>

      </div>

      <!-- Action Buttons Moved Below the Form -->
      <div class="modal-footer d-flex justify-content-center">
        <button class="btn btn-success" type="button" onclick="startAiSummary()">
          <i class="fa-solid fa-robot"></i> Process Request Now
        </button>
        <button class="btn copy" style="display: none;" type="button" onclick="copyToClipboard()">
          <i class="fa fa-copy"></i> Copy
        </button>
        <button class="btn btn-danger" data-dismiss="modal" type="button">Close</button>
      </div>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>

@section('scripts')
@include('common.ai-summary-js')
@endsection