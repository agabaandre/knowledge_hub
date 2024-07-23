<!-- First modal dialog -->
<div class="modal" id="import-modal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">Import Resources</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
            </div>
            <form method="post" action="{{ $action }}" enctype="multipart/form-data">
            <div class="modal-body text-left">
                @csrf

                <div class="row">

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="publication">Thematic Area</label>
                            @include('partials.publications.theme_dropdown',['field'=>'theme','class'=>'select2 theme',
                            'selected'=>(@$row->sub_thematic_area_id)?$row->sub_thematic_area_id:old('theme')])
                        </div>
                   </div>
                  <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="publication">Sub Theme</label>
                            @include('partials.publications.subtheme_dropdown',['field'=>'sub_theme','class'=>'select2 subtheme',
                            'selected'=>(@$row->sub_thematic_area_id)?$row->sub_thematic_area_id:''])
                        </div>
                   </div>

                   <div class="col-md-12">

                    <div class="mt-3">
                            <label class="form-label" for="publication">Choose File</label>
                            <input class="form-control" type="file" name="file">
                    </div>

                   </div>
                </div>
              

            </div>
            <div class="modal-footer">
                <!-- Toogle to second dialog -->
                <button type="submit" class="btn btn-outline-success btn-sm edit"><i class="fa fa-upload"></i> Import Resources</button>
                <button type="button" class="btn btn-outline-danger btn-sm edit">Cancel</button>
            </div>
            </form>
        </div>
    </div>
</div>
