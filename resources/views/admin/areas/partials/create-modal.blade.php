<div class="modal" id="create-area-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">Create Georgraphical Area</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
            </div>

            <form id="createAreaForm" enctype="multipart/form-data" action="{{ route('areas.store')}}" method="POST">
                @csrf
                <div class="modal-body">

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="area_name" required>
                    </div>

                    <div class="form-group">
                        <label for="name">ISO Code</label>
                        <input type="text" class="form-control" maxlength="2" minlength="2" name="iso_code" required>
                    </div>

                    <div class="form-group">
                        <label for="name">ISO 3 Code</label>
                        <input type="text" class="form-control" maxlength="3" minlength="3" name="iso3_code"  required>
                    </div>

                    <div class="form-group ">
                        <div class="row">
                                <label class="form-label">Flag (640 × 480 px)</label>
                                <input type="file" name="flag"/>
                                @error('flag')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <div class="row">
                                <label class="form-label">Map SVG</label>
                                <textarea  name="map" rows="4" class="form-control" name="map"></textarea>
                                @error('map')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <!-- Toogle to second dialog -->
                    <button class="btn btn-outline-danger btn-sm submit">Save Area</button>
                </div>
        </form>
        </div>
    </div>
</div>
