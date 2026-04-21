@php
    $hide_search = true;
@endphp

@extends('layouts.app')

@section('title', 'Edit discussion')

@section('content')
<div class="pt-5 pt-0 custom-bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div style="text-align: center; padding: 2rem 0;">
                    <h1 style="font-size: 2rem; font-weight: 700; margin: 0 0 0.5rem 0; color: white;">
                        <i class="fa fa-edit me-2"></i>Edit discussion
                    </h1>
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.95); font-size: 1rem;">
                        @if((int)($forum->is_rejected ?? 0) === 1)
                            Update your post and resubmit it for approval.
                        @else
                            Update your post while it is awaiting moderator approval.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.secondary_navigation', ['forceShow' => true])

<div class="container mt-4" style="margin-bottom: 4px;">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body text-left" style="padding: 2rem;">
                    @if(!empty($forum->rejected_reason))
                        <div class="alert alert-danger">
                            <strong>This post was not approved.</strong>
                            <div class="mt-2 mb-0">{{ $forum->rejected_reason }}</div>
                        </div>
                    @endif

                    @if(@$message)
                        <div class="alert alert-danger">{{ $message }}</div>
                    @endif

                    <form method="POST" action="{{ route('account.my-discussions.resubmit', $forum) }}" id="forum-resubmit-form" class="publications" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="title">Forum title</label>
                                    <input type="text" placeholder="Forum title" class="form-control url" id="title" name="title" required
                                           value="{{ old('title', $forum->forum_title) }}">
                                </div>
                            </div>

                            <div class="form-group col-lg-12">
                                <label class="form-label" for="communities">Target audience / communities of practice</label>
                                @include('partials.publications.publication_communities_dropdown', [
                                    'field' => 'communities[]',
                                    'selected' => $selectedCommunityIds ?? [],
                                    'show_hub_cop_options' => true,
                                    'also_public_on_hub' => (int) ($forum->also_public_on_hub ?? 0),
                                ])
                            </div>

                            <div class="form-group col-lg-12">
                                <label class="form-label" for="tags">Tags / health topics</label>
                                @include('partials.tags.dropdown', ['field' => 'tags[]', 'selected' => $selectedTagIds ?? []])
                                <small class="text-muted">Add relevant tags to help others find this discussion.</small>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="forum_cover">Cover image</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="image" id="forum_cover">
                                        <label class="custom-file-label" for="forum_cover">Choose file…</label>
                                        <div class="forum_cover_preview py-2">
                                            @if(!empty($forum->forum_image))
                                                <img src="{{ $forum->forum_image }}" alt="" style="max-height:160px;max-width:220px" class="rounded">
                                            @endif
                                        </div>
                                    </div>
                                    <small class="text-muted">Leave empty to keep the current image.</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="forum_attachments">Attachments</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="attachments[]" id="forum_attachments" multiple accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.odt,.ods,.odp,.rtf,video/*,audio/*,.mp3,.m4a,.wav,.aac,.ogg,.opus,.flac,.wma,.mp4,.webm,.mov,.avi,.mkv,.wmv,.flv,.3gp,.mpeg,.mpg">
                                        <label class="custom-file-label" for="forum_attachments">Add files…</label>
                                        <div class="forum_preview py-2"></div>
                                    </div>
                                    <small class="text-muted">New files are added to existing attachments.</small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="summernote">Forum story</label>
                                    <textarea placeholder="Description" class="form-control newform" id="summernote" name="description" required>{!! old('description', $forum->forum_description) !!}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4" style="display: flex; gap: 1rem; justify-content: flex-end;">
                            <a href="{{ route('account.my-discussions') }}" class="btn btn-secondary" type="button">Cancel</a>
                            <button class="btn btn-primary theme-bg text-white" type="submit">
                                @if((int)($forum->is_rejected ?? 0) === 1)
                                    <i class="fa fa-paper-plane me-2"></i>Resubmit for approval
                                @else
                                    <i class="fa fa-save me-2"></i>Save changes
                                @endif
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
        Array.from(input.files).forEach(function(file) {
            const reader = new FileReader();
            reader.onload = function(e){
                let html = '';
                if(file.type.startsWith('image/')){
                    html = '<div class="col-lg-4 mb-1"><img src="' + e.target.result + '" style="max-height:160px;max-width:220px" class="rounded"></div>';
                }else{
                    let icon = 'fa-file';
                    if(file.type.indexOf('pdf') !== -1) icon='fa-file-pdf';
                    else if(file.type.indexOf('powerpoint') !== -1) icon='fa-file-powerpoint';
                    else if(file.type.indexOf('word') !== -1) icon='fa-file-word';
                    else if(file.type.indexOf('excel') !== -1) icon='fa-file-excel';
                    else if(file.type.indexOf('audio') !== -1) icon='fa-file-audio';
                    else if(file.type.indexOf('video') !== -1) icon='fa-file-video';
                    html = '<div class="col-lg-4 mb-1"><i class="fas ' + icon + ' fa-3x"></i><div>' + file.name + '</div></div>';
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
            await page.render({ canvasContext: ctx, viewport: viewport }).promise;
            return new Promise(function(resolve, reject){
                canvas.toBlob(function(blob){
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
            const files = this.files;
            if(!files || !files.length) return;
            let pdfFile = null;
            for (let i = 0; i < files.length; i++) {
                if (files[i].type === 'application/pdf') { pdfFile = files[i]; break; }
            }
            const coverInput = $('#forum_cover')[0];
            if(pdfFile && coverInput && (!coverInput.files || !coverInput.files.length)){
                const cover = await forumExtractPDFCover(pdfFile);
                if(cover){
                    const dt = new DataTransfer();
                    dt.items.add(cover);
                    coverInput.files = dt.files;
                    const reader = new FileReader();
                    reader.onload = function(e){
                        $('.forum_cover_preview').html('<img src="' + e.target.result + '" style="max-height:160px;max-width:220px" class="rounded">');
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
