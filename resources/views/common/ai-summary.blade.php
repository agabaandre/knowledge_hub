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
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title" id="myExtraLargeModalLabel"><i class="fa fa-robot"></i> Content Summary <small>(AI Assisted)</small></h2>
          
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span class="ft-lg" aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body d-flex justify-content-center" style="max-height: 70vh; overflow-y: auto;">
          
          <div class="ai-content" id="coppable"></div>
  
        </div>
        <div class="modal-footer">
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