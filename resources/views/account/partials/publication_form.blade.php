
               
                <div class="row">

                @php
                    if( @$row && @$row->cover):
                        $image_link = storage_link('uploads/publications/'.$row->cover);
                    else:
                        $image_link = asset('assets/images/placeholder.jpg');
                    endif;
                @endphp

                <input type="hidden" name="id" id="id" class="newform" value="{{ @$row->id ?? old('id')}}">
                    
                <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="title">Publication Title</label>
                                <input placeholder="Title"  class="form-control newform"  id="title" name="title" 
                                value="{{ @$row->title ?? old('title')}}"
                                required/>
                            </div>
                 </div>

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
                        @include('partials.publications.filecategory_dropdown',['field'=>'file_type',
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
                    <button class="btn btn-success col-lg-12" type="submit">
                        {{ (@$row)?'Save Changes':'Submit'}}
                    </button>
                    </div>
                </div>
