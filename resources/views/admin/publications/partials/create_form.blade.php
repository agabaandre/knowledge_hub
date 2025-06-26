
<div class="modal-body">
               <div class="row">

                    <div class="col-md-6">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="description">Publication Title</label>
                                <textarea placeholder="Title" rows="3" class="form-control summernote-sm" id="title" 
                                name="title" 
                                required>{{ form_edit('title',$publication,'title') }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="description">Publication Description</label>
                                <textarea id="publication_description" placeholder="Descripion" class="form-control summernote" name="description" style="min-height: 400px;">{{ form_edit('description',$publication,'description') }}</textarea>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Source</label>
                                @include('partials.authors.dropdown',['field'=>'author','required'=>'required', 'selected'=>form_edit('author',$publication,'author_id')])
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Sub Theme</label>
                                @include('partials.publications.subtheme_dropdown',['field'=>'sub_theme','required'=>'required','selected'=>form_edit('sub_theme',$publication,'sub_thematic_area_id')])
                            </div>
                        </div>

                        <div class="col-md-12">
                            <!-- Section Label -->
                            <label class="form-label mb-3" for="publication">Publication Document Type</label>
                            <div class="mb-3">
                                <label class="form-check-inline">
                                    <input type="radio" name="upload_type" value="upload" checked class="form-check-input"> Attachment
                                </label>
                                <label class="form-check-inline">
                                    <input type="radio" name="upload_type" value="link" class="form-check-input">External Link
                                </label>

                                <div id="file-input">
                                    <!-- <label class="form-label" for="publication">Publication Doc</label> -->
                                    <input placeholder="Attach publication document" type="file" name="files" class="form-control" multiple>
                                     @if(@$publication && count(@$publication->attachments)>0)
                                     <h4 class="mt-3">Attachment(s): </h4>
                                        @foreach($publication->attachments as $attachment)
                                             <div class="py-2 mt-1"><a target="_blank" href="{{ storage_link('uploads/publications/') }}{{$attachment->file}}"><i class="fa fa-file fa-2x"></i> {{$attachment->description}}</a></div>
                                        @endforeach
                                    @endif
                                </div>

                                <div id="link-input" style="display:none;">
                                    <!-- <label class="form-label" for="publication">Publication URL Link</label> -->
                                    <input placeholder="Add publication link" type="text" name="link" class="form-control">
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="publication">File Type</label>
                                @include('partials.publications.filetype_dropdown',['field'=>'file_type','required'=>'required','selected'=>form_edit('file_type',$publication,'file_type_id')])
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="publication">File Category</label>
                                @include('partials.publications.filecategory_dropdown',['field'=>'category_id',
                                'selected'=>(@$publication->publication_catgory_id)?$publication->publication_catgory_id:''])
                            </div>
                        </div>
                     
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Search Tags</label>
                                @include('partials.tags.dropdown',['field'=>'tags[]','selected'=>form_edit('tags',$publication,'tag_ids')])
                            </div>
                        </div>

                        @if(states_enabled())
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Geographical Coverage</label>
                                @include('partials.publications.area_dropdown',['field'=>'geo_area_id','required'=>'required','selected'=>form_edit('geo_area_id',$publication,'geographical_coverage_id')])
                            </div>
                        </div>
                        @endif

                         <div class="col-md-12">
                            <label class="form-label" for="communities">Target Audience/Communities of Practice</label>
                            @include('partials.publications.publication_communities_dropdown',['field'=>'communities[]',
                                'selected'=>(@$publication->communities)?$publication->communities->pluck('id')->toArray():[]])
                        </div>

                        <div class="col-md-12">
                            <label class="form-label" for="accessgroups">Access Groups</label>
                            @include('partials.publications.accessgroups_dropdown',['field'=>'accessgroups[]',
                                'selected'=>(@$publication->accessgroups)?$publication->accessgroups->pluck('id')->toArray():[]])
                        </div>


                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Cover Image</label>
                                <div class="custom-file">
                                    <input type="file" class="form-control" name="cover" id="">
                                </div>
                                <div class="preview">
                                  @if(@$publication->cover)
                                    <img class="mt-3" src="{{storage_link('uploads/publications/'.@$publication->cover)}}" width="200px" />
                                  @endif
                                </div>
                            </div> 
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Status</label>
                                <select class="form-control select2" name="is_active" required>
                                    <option value="" selected disabled>Choose</option>
                                    <option value="Active" {{ (form_edit('is_active',$publication,'is_active')=='Active')?'selected':''}}>Active</option>
                                    <option value="In-Active" {{ (form_edit('is_active',$publication,'is_active')=='In-Active')?'selected':''}}>Inactive</option>

                                </select>
                            </div>
                        </div>

                        <!-- <div  class="col-md-4"> -->
                        <!-- </div> -->
                    </div>

                </div>
            </div>
            <div class="modal-footer">

                <button class="btn btn-danger" data-dismiss="modal" type="button">Cancel</button>
                <button class="btn btn-primary" type="submit">Save Record</button>
            </div>
