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
              <label class="form-label" for="country_id">Member state mapping <span class="text-muted">(optional)</span></label>
              <select class="form-control newform js-admin-unit-country" id="country_id" name="country_id">
                <option value="">— None —</option>
                @foreach(($hubCountries ?? $countries ?? collect()) as $country)
                <option value="{{ $country->id }}"
                    data-iso2="{{ strtoupper($country->iso_code ?? '') }}"
                    data-iso3="{{ strtoupper($country->iso3_code ?? '') }}"
                    {{ (int) old('country_id', $row->country_id ?? 0) === (int) $country->id ? 'selected' : '' }}>
                    {{ $country->name }}
                </option>
                @endforeach
              </select>
              <small class="text-muted">Link this unit to an AU member state for country-level KPI and map data joins.</small>
            </div>
          </div>

          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label" for="iso_code">ISO alpha-2 <span class="text-muted">(optional)</span></label>
              <input type="text" maxlength="2" placeholder="e.g. NG" class="form-control newform text-uppercase js-admin-unit-iso2" id="iso_code" name="iso_code" value="{{ strtoupper(old('iso_code', $row->iso_code ?? '')) }}">
            </div>
          </div>

          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label" for="iso3_code">ISO alpha-3 <span class="text-muted">(optional)</span></label>
              <input type="text" maxlength="3" placeholder="e.g. NGA" class="form-control newform text-uppercase js-admin-unit-iso3" id="iso3_code" name="iso3_code" value="{{ strtoupper(old('iso3_code', $row->iso3_code ?? '')) }}">
            </div>
          </div>

          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="parent_id">Parent Admin Unit</label>
              <select class="form-control newform no-select2"  id="parent_id" name="parent_id" >
                <option value="">None (top level)</option>
                @foreach(($allAdminUnits ?? $adminunits ?? collect()) as $unit)
                @if(! $row || (int) $unit->id !== (int) $row->id)
                <option {{ (($row && (int) $row->parent_id === (int) $unit->id) || (int) old('parent_id') === (int) $unit->id) ? 'selected' : '' }}
                     value="{{ $unit->id }}">{{ $unit->name }}@if($unit->parent) — under {{ $unit->parent->name }}@endif</option>
                @endif
                @endforeach
              </select>
              <small class="text-muted">A parent may have many children at the same level.</small>
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
