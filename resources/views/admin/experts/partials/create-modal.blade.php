<!--  Extra Large modal example -->
<div class="modal" id="create-modal{{ @$record->id ?? '' }}">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myExtraLargeModalLabel">{{ (@$record->id)? 'Edit':'Create' }} Expert</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <form action="{{ url('admin/experts/save') }}" method="post" id='filetypes' class='filetypes'>
        @csrf
      <div class="modal-body">
        <input type="hidden" name="id" id="id" value="{{ @$record->id ?? '' }}" class="newform">
        @php
            $modalIscoClassifications = $isco_classifications ?? [];
            $modalJobs = $jobs ?? [];
            $modalExpertTypes = $expert_types ?? $types ?? [];
        @endphp
        <div class="row">
          <div class="form-group col-md-6">
              <label class="form-label" for="name">First Name</label>
              <input type="text" placeholder="First Name" class="form-control" id="first_name" name="first_name" value="{{ @$record->first_name ?? old('first_name') }}" required>
          </div>

          <div class="form-group col-md-6">
              <label class="form-label" for="name">Last Name</label>
              <input type="text" placeholder="Last Name" class="form-control" id="last_name" name="last_name"
              value="{{ @$record->last_name ?? old('last_name') }}" 
              required>
          </div>

          
          <div class="form-group col-md-6">
              <label class="form-label" for="field">Field</label>
              <input type="text" placeholder="Work Field e.g Research" class="form-control" 
              value="{{ @$record->occupation ?? old('field') }}" 
              id="field" name="field" required>
          </div>

          <div class="form-group col-md-6">
              <label class="form-label" for="isco_classification_id">ISCO Classification</label>
              <select class="form-control js-example-basic-single" id="isco_classification_id" name="isco_classification_id" onchange="loadJobTitles(this)">
                  <option value="">Select ISCO Classification</option>
                  @foreach($modalIscoClassifications as $isco)
                      <option value="{{ $isco->id }}" data-isco-id="{{ $isco->isco_id }}" {{ (@$record->isco_classification_id == $isco->id || old('isco_classification_id') == $isco->id) ? 'selected' : '' }}>
                          {{ $isco->name }}
                      </option>
                  @endforeach
              </select>
          </div>

          <div class="form-group col-md-6">
              <label class="form-label" for="job_title_id">Job Title (from ISCO)</label>
              <select class="form-control js-example-basic-single" id="job_title_id" name="job_title_id">
                  <option value="">Select Job Title</option>
                  @if(@$record && @$record->job_title_id && @$record->iscoClassification)
                      @foreach($modalJobs as $job)
                          @if($job->isco_id == @$record->iscoClassification->isco_id)
                              <option value="{{ $job->id }}" {{ (@$record->job_title_id == $job->id) ? 'selected' : '' }}>
                                  {{ $job->name }}
                              </option>
                          @endif
                      @endforeach
                  @endif
              </select>
              <small class="form-text text-muted" id="job_titles_isco_hint">Select an ISCO classification first to load job titles</small>
          </div>

          <div class="form-group col-md-6">
              <label class="form-label" for="job_title">Job Title (Free Text - Optional)</label>
              <input type="text" placeholder="Job Title e.g Physician" class="form-control" id="job_title" name="job_title"
              value="{{ @$record->job_title ?? old('job_title') }}">
          </div>

          <div class="form-group col-md-6">
              <label class="form-label" for="email">Email</label>
              <input type="email" placeholder="Email" class="form-control" id="email" name="email" 
              value="{{ @$record->email ?? old('email') }}" required>
          </div>

          <div class="form-group col-md-6">
              <label class="form-label" for="phone_number">Phone Number</label>
              <input type="tel" placeholder="Phone Number" class="form-control" id="phone_number" name="phone_number"
              value="{{ @$record->phone_number ?? old('phone_number') }}">
          </div>

          <div class="form-group col-md-6">
              <label class="form-label" for="type_id">Expert Type</label>
              <select class="form-control js-example-basic-single" id="type_id" name="type_id" required>
                  <option value="">Select Expert Type</option>
                  @foreach($modalExpertTypes as $type)
                      <option value="{{ $type->id }}" {{ (@$record->expert_type_id == $type->id || old('type_id') == $type->id) ? 'selected' : '' }}>
                          {{ $type->type_name ?? $type->name }}
                      </option>
                  @endforeach
              </select>
          </div>

          <div class="form-group col-md-6">
              <label class="form-label" for="country">Country</label>
              @include('partials.countries.dropdown',['selected'=>@$record->country_id])
          </div>

        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-danger" data-dismiss="modal" type="button">Cancel</button>
        <button class="btn btn-primary" type="submit">Submit Record</button>
      </div>

      </form>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
