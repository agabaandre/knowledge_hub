
               
                <div class="row">

                <input type="hidden" name="id" id="id" class="newform">
                    
                <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="description">Publication Title</label>
                                <input placeholder="Title"  class="form-control newform"  id="title" name="title" required/>
                            </div>
                 </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label" for="publication">File Type</label>
                        @include('partials.publications.filetype_dropdown',['field'=>'file_type'])
                    </div>
                </div>

                <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Sub Theme</label>
                                @include('partials.publications.subtheme_dropdown',['field'=>'sub_theme','class'=>'select2'])
                            </div>
                 </div>

                 <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Geographical Coverage</label>
                                @include('partials.publications.area_dropdown',['class'=>'select2'])
                            </div>
                </div>

                 
                 <div class="col-md-12 url_wrapper">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Publication URL Link</label>
                                <input type="text" placeholder="URL Link" class="form-control url" id="publication" name="link" required>
                            </div>
                        </div>
                       
                        
                 
                <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="summernote">Publication Description</label>
                                <textarea placeholder="Descripion" class="form-control newform" id="summernote" name="description" required></textarea>
                            </div>
                        </div>

                   
                        <div class="col-md-6" >
                            <div class="mb-3">
                                <label class="form-label" for="publication">Cover Image</label>
                                <div class="custom-file">
                                    <input type="file" style="display: none;" name="cover" id="cover">
                                   <div onclick="$('#cover').click()" class="cover_preview py-2 rounded" style="max-width:300px; min-height:200px; max-height:550px; background-image: url({{ asset('assets/images/placeholder.jpg') }}); background-size:cover; background-position:center; background-repeat:no-repeat;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 attachment" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Publication Attachments</label>
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
                    <button class="btn btn-success col-lg-12" type="submit">Submit</button>
                    </div>
                </div>
