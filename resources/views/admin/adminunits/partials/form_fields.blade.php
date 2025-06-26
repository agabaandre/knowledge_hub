@csrf
        <input type="hidden" name="id" id="id" class="newform" value="{{$row->id ?? '' }}">
        <div class="row">
          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="unit_name">Admin Unit Name</label>
              <input type="text" placeholder="Enter Unit Name" class="form-control newform" id="unit_name" name="unit_name" value="{{$row->name ?? old('unit_name')}}" required>
            </div>
          </div>

          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="code">Identifier Code</label>
              <input type="text" placeholder="Enter Code" class="form-control newform" id="code" name="code" value="{{$row->code ?? old('code')}}">
            </div>
          </div>

          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="alt_code">Alternate Identifier Code</label>
              <input type="text" placeholder="Enter Alternate Code" class="form-control newform" id="alt_code" name="alt_code" value="{{$row->alternate_code ?? old('alt_code')}}">
            </div>
          </div>

          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="description">Description</label>
              <textarea placeholder="Enter Description" class="form-control newform"  id="description" name="description" >{!! $row->description ?? old('description') !!}</textarea>
            </div>
          </div>

          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="parent_id">Parent Admin Unit</label>
              <select class="form-control newform"  id="parent_id" name="parent_id" >
                <option value="">None</option>
                @foreach($adminunits as $unit)
                <option {{ (($row && $row->parent_id ==$unit->id) ||  old('alt_code')==$unit->id)?'selected':''}}
                     value="{{$unit->id}}">{{$unit->name}}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="col-md-12 mt-3">
                <label> Logo</label>
                <div class="form-group">
                    <input type="file" name="logo" id="attachments">
                    <div class="preview" style="max-width: 150px;">
                        <img src="{{ ($row && $row->logo)?storage_link("uploads/adminunits/".$row->logo) : asset("assets/images/placeholder.pg") }}" width="50px" class="img img-thumbnail"/>
                    </div>
                </div>
            </div>
        </div>