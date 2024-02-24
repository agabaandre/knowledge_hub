
@php
      if( @$row && @$row->cover):
          $image_link = storage_link('uploads/publications/'.$row->cover);
      else:
          $image_link = asset('assets/images/placeholder.jpg');
      endif;
  @endphp

<!-- SmartWizard html -->
<div id="smartwizard" class="mt-3">
    <ul class="nav">
        <li>
            <a class="nav-link" href="#step-1"> <strong>Step 1</strong>
                <br>Start Here</a>
        </li>
        <li>
            <a class="nav-link" href="#step-2"> <strong>Step 2</strong>
                <br>Resource categorization</a>
        </li>
        <li>
            <a class="nav-link" href="#step-3"> <strong>Step 3</strong>
                <br>Resource Details</a>
        </li>

        <li>
            <a class="nav-link" href="#step-4"> <strong>Step 4</strong>
                <br>Resource Attachments</a>
        </li>

    </ul>

   
    <div class="tab-content">
        <div id="step-1" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
           <div class="row">

               <div class="col-md-12 mt-3 ml-3">
                        <label class="form-check-inline px-2">
                              <input type="radio" name="upload_type" value="upload" checked class="form-check-input"> Attachment
                          </label>
                        <label class="form-check-inline px-2">
                              <input type="radio" name="upload_type" value="link" class="form-check-input">External Link
                        </label>
                </div>

                <div class="col-md-12 mt-3">

                     <input type="hidden" name="id" id="id" class="newform" value="{{ @$row->id ?? old('id')}}">
                     

                    <h3>What is the title of the resource you what to publish?</h3>

                        <div class="mb-3">
                            <input placeholder="Resource Title"  class="form-control newform"  id="title" name="title" 
                            value="{{ @$row->title ?? old('title')}}"
                            required/>
                        </div>
                 </div>

                </div>

        </div>


        <div id="step-2" class="tab-pane" role="tabpanel" aria-labelledby="step-2">
          <br>
          <h3>Choose resource categorization</h3>
          <div class="row">

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label" for="publication">File Type</label>
                        @include('partials.publications.filetype_dropdown',['field'=>'file_type',
                        'selected'=>(@$row->file_type_id)?$row->file_type_id:''])
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label" for="publication">File Category</label>
                        @include('partials.publications.filecategory_dropdown',['field'=>'category_id',
                        'selected'=>(@$row->publication_category_id)?$row->publication_category_id:''])
                    </div>
                </div>
                
                <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Sub Theme</label>
                                @include('partials.publications.subtheme_dropdown',['field'=>'sub_theme','class'=>'select2',
                                'selected'=>(@$row->sub_thematic_area_id)?$row->sub_thematic_area_id:''])
                            </div>
                 </div>

                </div>
            
        </div>

        <div id="step-3" class="tab-pane" role="tabpanel" aria-labelledby="step-3">
          <br>
          <h3>Provide publication details</h3>
          <div class="row">

              <div class="col-md-12 url_wrapper">
                        <div class="mb-3">
                            <label class="form-label" for="publication">Publication URL Link</label>
                            <input type="text" placeholder="URL Link" class="form-control url" id="publication" 
                              name="link"  value="{{ @$row->publication ?? old('publication')}}">
                        </div>
              </div>
                   
            <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="summernote">Publication Description</label>
                            <textarea placeholder="Descripion" class="form-control newform" id="summernote"
                             name="description" required>{!! $row->description ?? old('description') !!}</textarea>
                        </div>
              </div>

          </div>
            
        </div>

        <div id="step-4" class="tab-pane" role="tabpanel" aria-labelledby="step-4">
          <br>
          <h3>Provide Attachments</h3>
          <div class="row" style="min-height: 300px;">

                      <div class="col-md-12">
                          <div class="mb-3">
                              <label class="form-label" for="publication">Search Tags</label>
                              @include('partials.tags.dropdown',['field'=>'tags[]','selected'=>form_edit('tags',@$row->publication_catgory_id,'tag_ids')])
                          </div>
                      </div>
                        <div class="col-md-6" >
                            <div class="mb-3">
                                <label class="form-label" for="publication">Cover Image</label>
                                <div class="custom-file">
                                    <input type="file" style="display: none;" name="cover" id="cover">
                                   <div onclick="$('#cover').click()" class="cover_preview py-2 rounded" style="max-width:300px; min-height:200px; max-height:550px; background-image: url({{ $image_link }}); background-size:cover; background-position:center; background-repeat:no-repeat;">
                                    </div>
                                </div>
                            </div>
                        </div>

                    <div class="col-md-6 attachment" >
                      <div class="mb-3">
                                <label class="form-label" for="publication">Publication Attachments</label>

                                @if (@$row && @$row->has_attachments)
                  
                    @php
                    $count = 1;
                    @endphp

                    @foreach($publication->attachments as $pub_file)
                      <a href="{{ url('uploads/publications') }}?id={{$pub_file->file}}" target="_blank" class="btn btn-md rounded bg-white border fs-sm ft-medium"><i class="fa fa-download"></i> View Attachment {{ $count }}</a>
                    @php
                      $count++;
                    @endphp
                    @endforeach;
                @endif

                 <div class="custom-file">
                          <input  type="file" class="custom-file-input" name="files" id="attachments">
                          <label class="custom-file-label" for="validatedCustomFile">Choose file.s..</label>
                          <div class="preview py-2"></div>
                      </div>
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="communities">Target Audience/Communities of Practice</label>
                    <!-- <a href="#" class="btn btn-sm btn-dark btn-outline mb-2"><i class="fa fa-plus"></i> Add Community Of Practice</a> -->
                     @include('partials.publications.publication_communities_dropdown',['field'=>'communities[]',
                        'selected'=>(@$row->communities)?$row->communities:[]])
                  </div>


                  </div>

                  <div class="col-md-6 justify-content-center video" style="display: none;">
                       <label class="form-label" for="publication">Video</label>
                      <div class="mb-3">
                          <iframe width="450" height="260"class="vid" src="">
                          </iframe>
                      </div>
                  </div>

          </div>

          <div class="row mt-3 mb-3">
                    <div class="col-lg-8 mt-5  float-end">
                    </div>
                    <div class="col-lg-3 mt-5  float-end">
                    <button class="btn btn-dark col-lg-12" type="submit">
                        {{ (@$row)?'Save Changes':'Submit'}}
                    </button>
                    </div>
           </div>
            
        </div>


    </div>


    <!-- Include optional progressbar HTML -->
    <div class="progress">
      <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <br>


</div>
 

<script type="text/javascript">
      
  $(document).ready(function() {

    $('input[name="upload_type"]').on('change', function() {
        if ($(this).val() == 'upload') {
            $('.url_wrapper').show();
            $('.url_wrapper').hide();
        } else {
            $('.url_wrapper').hide();
            $('.url_wrapper').show();
        }
    });



    // Smart Wizard
    $('#smartwizard').smartWizard({
      autoAdjustHeight: false,
      selected: 0,
      theme: 'dots',
      toolbarSettings: {
        toolbarPosition: 'both', // both bottom
      },

    });

    // Step show event
    $("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection, stepPosition) {

      $("#prev-btn").removeClass('disabled');
      $("#next-btn").removeClass('disabled');

      if (stepPosition === 'first') {
        $("#prev-btn").addClass('disabled');
      } 
      else if (stepPosition === 'last') {
        $("#next-btn").addClass('disabled');
      } 
      else {
        $("#prev-btn").removeClass('disabled');
        $("#next-btn").removeClass('disabled');
      }

    });


    $("#prev-btn").on("click", function() {
      // Navigate previous
      $('#smartwizard').smartWizard("prev");
      return true;
    });


    $("#next-btn").on("click", function() {
      // Navigate next
      $('#smartwizard').smartWizard("next");
      return true;
    });
    

  });

  
  </script>