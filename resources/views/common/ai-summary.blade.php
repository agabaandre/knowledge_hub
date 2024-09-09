<style>
	.modal-fullscreen {
		width: 90%;
		min-height: 90vh!important;
		max-height: 90vh!important;
		margin: 5%;
		padding: 0;
		max-width: none;
	}
</style>
	<!--  Extra Large modal example -->
<div class="modal" id="summarise-modal">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title" id="myExtraLargeModalLabel"><i class="fa fa-robot"></i> Content Summary <small>(AI Assisted)</small></h2>
          
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span class="ft-lg" aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">

          @php
            $user = current_user();
          @endphp

          <div class="row mb-10  d-flex justify-content-center">
            <div class="form-group">
              <label>Choose Summary Language</label>
              <select class="form-control language">
                <option {{ ($user && 'en' == $user->langauge) ? 'selected' : '' }}>English</option>
                <option {{ ($user && 'ar' == $user->langauge) ? 'selected' : '' }}>Arabic</option>
                <option {{ ($user && 'fr' == $user->langauge) ? 'selected' : '' }}>French</option>
                <option {{ ($user && 'pt' == $user->langauge) ? 'selected' : '' }}>Portuguese</option>
                <option {{ ($user && 'es' == $user->langauge) ? 'selected' : '' }}>Spanish</option>
                <option {{ ($user && 'sw' == $user->langauge) ? 'selected' : '' }}>Swahili</option>
              </select>
            </div>
          </div>
          
          <div class="row  d-flex justify-content-center">
            <div class="ai-content" id="coppable" style="padding: 30px;"></div>
          </div>

        </div>
        <div class="modal-footer">
          <button class="btn btn-success"  type="button" onclick="startAiSummary()">
            <span aria-hidden="true"><i class="fa fa-magic"></i> Summarise Now</span>
          </button>
          <button class="btn copy" style="display: none;" type="button" onclick="copyToClipboard()">
            <span aria-hidden="true"><i class="fa fa-copy"></i> Copy</span>
          </button>
          <button class="btn btn-danger" data-dismiss="modal" type="button">Close</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div>

  
@section('scripts')
    @include('common.ai-summary-js')
@endsection