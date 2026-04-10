@php
    $hide_search = true;
@endphp

@extends('layouts.app')

@section('title', 'Create Forum')

@section('styles')

@endsection

@section('content')
{{-- Custom Header Section (replaces search bar) --}}
<div class="pt-5 pt-0 custom-bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div style="text-align: center; padding: 2rem 0;">
                    <h1 style="font-size: 2rem; font-weight: 700; margin: 0 0 0.5rem 0; color: white;">
                        <i class="fa fa-comments me-2"></i>Create Forum
                    </h1>
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.95); font-size: 1rem;">
                        Start a new discussion, interested individuals will be alert and will join you!
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Secondary Navigation Below Banner --}}
@include('partials.secondary_navigation', ['forceShow' => true])

<div class="container mt-4" style="margin-bottom: 4px;">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body text-left" style="padding: 2rem;">
                    @if(@$message)
                     <div class="alert alert-danger">{{ $message }}</div>
                    @endif
                   <form method="POST" action="{{ route('forums.publish') }}" id='publications' class='publications' enctype="multipart/form-data">
                    @csrf
                       <div class="row">
                    
                 <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Forum Title</label>
                                <input type="text" placeholder="Forum Title" class="form-control url" id="title" name="title" required>
                            </div>
                  </div>

                  
                  <div class="form-group col-lg-12">
                    <label class="form-label" for="communities">Target Audience/Communities of Practice</label>
                      @include('partials.publications.publication_communities_dropdown', [
                          'field' => 'communities[]',
                          'selected' => [],
                          'show_hub_cop_options' => true,
                      ])
                  </div>
                  
                  <div class="form-group col-lg-12">
                      <label class="form-label" for="tags">Tags/Health Topics</label>
                      @include('partials.tags.dropdown',[ 'field'=>'tags[]', 'selected'=>[] ])
                      <small class="text-muted">Add relevant tags to help others find this discussion.</small>
                  </div>
                  
                  <!-- Two-column media row -->
                  <div class="col-md-6">
                      <div class="mb-3">
                          <label class="form-label" for="forum_cover">Cover Image</label>
                          <div class="custom-file">
                              <input type="file" class="custom-file-input" name="image" id="forum_cover">
                              <label class="custom-file-label" for="validatedCustomFile">Choose file...</label>
                              <div class="forum_cover_preview py-2"></div>
                          </div>
                      </div>
                  </div>

                  <div class="col-md-6">
                      <div class="mb-3">
                          <label class="form-label" for="attachments">Attachments</label>
                          <div class="custom-file">
                              <input  type="file" class="custom-file-input" name="attachments" id="forum_attachments" multiple>
                              <label class="custom-file-label" for="validatedCustomFile">Choose file.s..</label>
                              <div class="forum_preview py-2"></div>
                          </div>
                      </div>
                  </div>

                  <!-- Forum story below attachments/cover -->
                  <div class="col-md-12">
                      <div class="mb-3">
                          <label class="form-label" for="summernote">Forum Story</label>
                          <textarea placeholder="Descripion" class="form-control newform" id="summernote" name="description" required></textarea>
                      </div>
                  </div>
      
                       </div>

                       <div class="form-group mt-4" style="display: flex; gap: 1rem; justify-content: flex-end;">
                           <a href="{{ url('forums') }}" class="btn btn-secondary" type="button">Cancel</a>
                           <button class="btn btn-primary theme-bg text-white" type="submit">
                               <i class="fa fa-paper-plane me-2"></i>Submit Forum
                           </button>
                       </div>

                   </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

    @include('account.partials.create_js')
    @include('common.select2')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    function forumPreviewFiles(input, previewContainer){
        const $preview = $(previewContainer);
        $preview.empty();
        if(!input.files || !input.files.length) return;
        Array.from(input.files).forEach((file, idx) => {
            const reader = new FileReader();
            reader.onload = function(e){
                let html = '';
                if(file.type.startsWith('image/')){
                    html = `<div class="col-lg-4 mb-1"><img src="${e.target.result}" style="max-height:160px;max-width:220px" class="rounded"></div>`;
                }else{
                    let icon = 'fa-file';
                    if(file.type.includes('pdf')) icon='fa-file-pdf';
                    else if(file.type.includes('powerpoint')) icon='fa-file-powerpoint';
                    else if(file.type.includes('word')) icon='fa-file-word';
                    else if(file.type.includes('excel')) icon='fa-file-excel';
                    else if(file.type.includes('audio')) icon='fa-file-audio';
                    else if(file.type.includes('video')) icon='fa-file-video';
                    html = `<div class="col-lg-4 mb-1"><i class="fas ${icon} fa-3x"></i><div>${file.name}</div></div>`;
                }
                $preview.append(html);
            };
            reader.readAsDataURL(file);
        });
    }

    async function forumExtractPDFCover(pdfFile){
        try {
            const arrayBuffer = await pdfFile.arrayBuffer();
            const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
            const page = await pdf.getPage(1);
            const viewport = page.getViewport({ scale: 2.0 });
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            canvas.width = viewport.width; canvas.height = viewport.height;
            await page.render({ canvasContext: ctx, viewport }).promise;
            return new Promise((resolve, reject)=>{
                canvas.toBlob((blob)=>{
                    if(!blob) return reject('Failed to create cover image');
                    const file = new File([blob], pdfFile.name.replace(/\.pdf$/i,'_cover.png'), { type: 'image/png' });
                    resolve(file);
                }, 'image/png');
            });
        } catch (e){ console.error(e); return null; }
    }

    $(function(){
        $('#forum_attachments').on('change', async function(){
            forumPreviewFiles(this, '.forum_preview');
            // Extract cover from first PDF if cover not set
            const files = this.files; if(!files || !files.length) return;
            let pdfFile = Array.from(files).find(f => f.type === 'application/pdf');
            if(pdfFile && (!$('#forum_cover')[0].files || !$('#forum_cover')[0].files.length)){
                const cover = await forumExtractPDFCover(pdfFile);
                if(cover){
                    const dt = new DataTransfer();
                    dt.items.add(cover);
                    $('#forum_cover')[0].files = dt.files;
                    // update cover preview
                    const reader = new FileReader();
                    reader.onload = function(e){
                        $('.forum_cover_preview').html(`<img src="${e.target.result}" style="max-height:160px;max-width:220px" class="rounded">`);
                    };
                    reader.readAsDataURL(cover);
                }
            }
        });

        $('#forum_cover').on('change', function(){
            forumPreviewFiles(this, '.forum_cover_preview');
        });
    });
    </script>

@endsection
