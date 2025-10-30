@extends('layouts.app')

@section('styles')
{{-- Summernote CSS loaded via partial in scripts to match forums --}}
<style>
    body {
        background: #f4f6f9;
    }
    .section-heading {
        font-size: 1.25rem;
        font-weight: 600;
        color: #911C39;
        margin-bottom: 1rem;
    }
    .card-md {
        background: #fff;
        border-radius: 0.75rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    .badge-au {
        background-color: #119A48;
        color: #fff;
        font-size: 0.85rem;
        border-radius: 50px;
        padding: 0.35rem 0.75rem;
        margin-right: 0.5rem;
    }
    .btn-au {
        background-color: #119A48;
        color: #fff;
    }
    .btn-au:hover {
        background-color: #0e7a3a;
        color: #fff;
    }
    .comment-box {
        background: #f1f1f1;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .meta-label {
        font-weight: 500;
        color: #5F5F5F;
        font-size: 0.9rem;
    }
    .meta-value {
        display: block;
        margin-bottom: 1rem;
        font-size: 0.95rem;
    }
</style>
@endsection

@section('content')
@php $likes = count($publication->favourited); @endphp
<section class="py-5" style="background: #fff;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-3 text-center mb-3">
                <img src="{{ $publication->image_url }}" class="img-fluid shadow rounded" alt="Cover Image">
            </div>
            <div class="col-md-6">
                <h3 class="font-weight-bold mb-2">{{ $publication->title }}</h3>
                <p class="text-muted">{{ $publication->theme->description ?? '' }}</p>
                <div class="d-flex flex-wrap mb-3">
                    <span class="badge badge-au">
                        {{ !$publication->is_version ? $publication->sub_theme->description ?? '' : 'Version ' . $publication->version_no }}
                    </span>
                    @if($likes)
                        <span class="badge badge-dark">
                            <i class="lni lni-heart"></i> {{ $likes }} like{{ $likes > 1 ? 's' : '' }}
                        </span>
                    @endif
                </div>
                <button onclick="summarise({{ $publication->id }})" class="btn btn-au btn-sm">
                    <i class="fa-solid fa-microchip"></i> AI Processing (Summarizer)
                </button>
            </div>
            <div class="col-md-3 mt-3 mt-md-0">
                @if ($publication->publication)
                    <a href="{{ $publication->publication }}" target="_blank" class="btn btn-outline-success btn-block mb-2">
                        <i class="fa fa-eye"></i> Browse Resource
                    </a>
                @endif
                @auth
                    <a href="{{ route('account.newversion') }}?id={{ $publication->id }}" class="btn btn-outline-danger btn-block mb-2">
                        <i class="fa fa-plus"></i> Submit Version
                    </a>
                    <a href="{{ route('account.summarize') }}?id={{ $publication->id }}" class="btn btn-outline-danger btn-block">
                        <i class="fa fa-file"></i> Submit Summary
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>

<section class="py-4">
    <div class="container">
        <div class="row">
            <!-- Left -->
            <div class="col-lg-8">
                <div class="card-md">
                    @if ($publication->is_embedded)
                        <div class="responsive-iframe-container mb-4">
                            <iframe src="{{ $publication->publication }}" allowfullscreen></iframe>
                        </div>
                    @elseif ($publication->is_video)
                        <div class="mb-4">
                            <iframe width="100%" height="400" src="{{ $publication->publication }}"></iframe>
                        </div>
                    @endif
                    <h5 class="section-heading">Description</h5>
                    <p>{!! $publication->description !!}</p>
                </div>

                <div class="card-md">
                    <h5 class="section-heading">Comments ({{ count($publication->comments) }})</h5>
                    @auth
                        <form id="commentForm" action="{{ url('records/comment') }}" method="post" class="mb-3">
                            @csrf
                            <input type="hidden" name="publication_id" value="{{ $publication->id }}">
                            <input type="hidden" name="user_id" value="{{ current_user()->user_id }}">
                            <div class="form-group">
                                <label class="mb-2">Write a comment</label>
                                <textarea id="commentEditor" name="comment" class="summernote-sm"></textarea>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-au btn-sm">Post</button>
                            </div>
                        </form>
                    @else
                        <p class="text-muted">Login to comment.</p>
                        <a href="{{ url('/login') }}" class="btn btn-outline-primary btn-sm">Login</a>
                    @endauth

                    <div id="commentsList">
                        @foreach ($publication->comments as $comment)
                            <div class="comment-box">
                                <strong>{{ $comment->user->name ?? 'Anonymous' }}</strong>
                                <small class="text-muted d-block">{{ time_ago($comment->created_at) }}</small>
                                <div class="mb-0">{!! nl2br(e($comment->comment)) !!}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right -->
            <div class="col-lg-4">
                <div class="card-md">
                    <h5 class="section-heading">Resource Details</h5>
                    <div>
                        <label class="meta-label">Source</label><span class="meta-value">{{ $publication->author->name }}</span>
                        <label class="meta-label">Visits</label><span class="meta-value">{{ $publication->visits }}</span>
                        @if(!empty($publication->year_published))
                        <label class="meta-label">Year</label><span class="meta-value">{{ $publication->year_published }}</span>
                        @endif
                        <label class="meta-label">Likes</label><span class="meta-value">{{ $likes }}</span>
                        <label class="meta-label">Comments</label><span class="meta-value">{{ count($publication->comments) }}</span>
                        <label class="meta-label">Category</label><span class="meta-value">{{ @$publication->data_category->category_name }}</span>
                        <label class="meta-label">Sub Category</label><span class="meta-value">{{ $publication->sub_category->category_name ?? '' }}</span>
                        <label class="meta-label">Theme</label><span class="meta-value">{!! $publication->theme->description ?? '' !!}</span>
                        <label class="meta-label">Sub-Theme</label><span class="meta-value">{!! nl2br($publication->sub_theme->description ?? '') !!}</span>
                        <label class="meta-label">Associated Authors</label><span class="meta-value">{{ $publication->associated_authors ?? 'N/A' }}</span>
                    </div>
                    @include('common.favourites_btn',['row'=>$publication])
                </div>

                @if ($publication->has_attachments)
                    <div class="card-md">
                        <h5 class="section-heading">Attachments</h5>
                        <ul class="list-group">
                            @foreach ($publication->attachments as $i => $file)
                                @php
                                    $url = $file->file;
                                    $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
                                    $office = in_array($ext, ['ppt','pptx','doc','docx','xls','xlsx']) ? 1 : 0;
                                @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fa fa-paperclip text-muted mr-2"></i> {{ $file->description ?? 'Attachment ' . ($i + 1) }}</span>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-secondary preview-attachment"
                                                data-file-url="{{ $url }}" data-file-ext="{{ $ext }}" data-file-office="{{ $office }}">
                                            <i class="fa fa-eye"></i> Preview
                                        </button>
                                        <a class="btn btn-outline-primary" href="{{ $url }}" target="_blank">
                                            <i class="fa fa-download"></i>
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (count($publication->versioning) || $publication->parent_id > 0)
                    <div class="card-md">
                        <h5 class="section-heading">Versions</h5>
                        <ul class="list-group">
                            @foreach ($publication->versioning as $version)
                                <li class="list-group-item">
                                    <a href="{{ url('records/resource') }}?id={{ $version->id }}">Version {{ $version->version_no }}</a>
                                </li>
                            @endforeach
                            @if ($publication->parent_id > 0)
                                <li class="list-group-item">
                                    <a href="{{ url('records/resource') }}?id={{ $publication->parent_id }}">
                                        <i class="fa fa-link"></i> Original Version
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endif

                @if (count($publication->summaries))
                    <div class="card-md">
                        <h5 class="section-heading">Summaries & Abstracts</h5>
                        <ul class="list-group">
                            @foreach ($publication->summaries as $summary)
                                @if ($comment->is_approved == 1)
                                    <li class="list-group-item">
                                        <a href="{{ url('records/shortened') }}?id={{ $summary->id }}">
                                            {{ truncate($summary->title, 100) }} by {{ $summary->author->name ?? '' }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card-md">
                    <h5 class="section-heading">Rate this Resource</h5>
                    @include('partials.general.rating')
                </div>

                <div class="card-md">
                    <h5 class="section-heading">Share This Resource</h5>
                    {{ share_buttons(url('records/resource') . '?id=' . $publication->id) }}
                </div>
            </div>
        </div>
    </div>
</section>
@include('common.ai-summary')
<!-- Modal for preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:95%">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="previewModalLabel">Attachment Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="previewModalBody" style="min-height:70vh;display:flex;align-items:center;justify-content:center;background:#f8fafc;">
        <div class="text-center w-100">Loading preview...</div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
@include('partials.general.summernote')
<script>
$(document).on('click', '.preview-attachment', function() {
    var fileUrl = $(this).data('file-url');
    var ext = ($(this).data('file-ext') || '').toString();
    var isOffice = ($(this).data('file-office') || '0').toString() === '1';
    var modalBody = $('#previewModalBody');
    var content = '';
    if(['jpg','jpeg','png','gif','webp'].includes(ext)) {
        content = '<img src="'+fileUrl+'" class="img-fluid" style="max-height:75vh;max-width:100%;margin:auto;display:block;">';
    } else if(ext === 'pdf') {
        content = '<iframe src="'+fileUrl+'#toolbar=1&navpanes=0&scrollbar=1" style="width:100%;height:75vh;border:none;"></iframe>';
    } else if(isOffice) {
        var gdocs = 'https://docs.google.com/viewer?url='+encodeURIComponent(fileUrl)+'&embedded=true';
        content = '<iframe src="'+gdocs+'" style="width:100%;height:75vh;border:none;"></iframe>';
    } else {
        content = '<div class="alert alert-info">Preview not available. <a href="'+fileUrl+'" target="_blank">Download/Open file</a></div>';
    }
    modalBody.html(content);
    var modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
});

// Async comments with Summernote-like lightweight editor and 1MB upload cap
$(function(){
    function ensureSummernoteLoaded(callback){
        if ($.fn && $.fn.summernote) { callback(); return; }
        // ensure CSS present
        var cssLoaded = false;
        $('link').each(function(){ if(this.href && this.href.indexOf('summernote')>-1) cssLoaded=true; });
        if(!cssLoaded){
            $('<link>', { rel:'stylesheet', href:'{{ asset('assets/plugins/summernote/dist/summernote.min.css') }}' }).appendTo('head');
        }
        // try local JS then fallback to CDN
        var script = document.createElement('script');
        script.src = '{{ asset('assets/plugins/summernote/dist/summernote.min.js') }}';
        script.onload = callback;
        script.onerror = function(){
            var cdn = document.createElement('script');
            cdn.src = 'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js';
            cdn.onload = function(){
                if(!cssLoaded){
                    $('<link>', { rel:'stylesheet', href:'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css' }).appendTo('head');
                }
                callback();
            };
            document.body.appendChild(cdn);
        };
        document.body.appendChild(script);
    }

    var $editor = $('#commentEditor');
    if ($editor.length && !$editor.data('summernote')) {
        ensureSummernoteLoaded(function(){
        try {
            if (!$editor.hasClass('summernote') && !$editor.hasClass('summernote-sm')) {
                $editor.addClass('summernote-sm');
            }
            $editor.summernote({
                placeholder: 'Write a comment... (images ≤ 1MB)',
                height: 120,
                toolbar: [
                    ['style', ['bold','italic','underline']],
                    ['para', ['ul','ol']]
                ],
                callbacks: {
                    onImageUpload: function(files){
                        if(!files || !files.length) return;
                        var file = files[0];
                        if (file.size > 1024*1024) { // 1MB
                            alert('Please upload images up to 1MB.');
                            return;
                        }
                        // Use centralized upload endpoint
                        var data = new FormData();
                        data.append('file', file);
                        data.append('_token', '{{ csrf_token() }}');
                        $.ajax({
                            url: '{{ route('image.upload') }}',
                            type: 'POST',
                            data: data,
                            cache: false,
                            contentType: false,
                            processData: false
                        }).done(function(resp){
                            var imageUrl = resp.url || resp;
                            $editor.summernote('insertImage', imageUrl);
                        }).fail(function(){
                            alert('Image upload failed.');
                        });
                    }
                }
            });
        } catch(e) {
            // fallback
            $editor.replaceWith('<textarea id="commentEditor" class="form-control" rows="3"></textarea>');
        }
        });
    }

    $('#commentForm').on('submit', function(e){
        e.preventDefault();
        var html;
        if ($('#commentEditor').data('summernote')) {
            html = $('#commentEditor').summernote('code');
            // ensure textarea has the html value for serialize
            $('#commentEditor').val(html);
        } else {
            html = $('#commentEditor').val();
        }
        // simple guard: empty or only tags
        if (!html || $('<div>').html(html).text().trim().length === 0) {
            alert('Please write a comment.');
            return;
        }
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize()
        }).done(function(resp){
            // optimistic render new comment at top
            var nowText = 'just now';
            var name = '{{ current_user()->name ?? "You" }}';
            var safeHtml = html; // server sanitization should also occur
            var item = '<div class="comment-box">'
                + '<strong>'+ name +'</strong>'
                + '<small class="text-muted d-block">'+ nowText +'</small>'
                + '<div class="mb-0">'+ safeHtml +'</div>'
                + '</div>';
            $('#commentsList').prepend(item);
            // clear editor
            if ($('#commentEditor').data('summernote')) {
                $('#commentEditor').summernote('reset');
                $('#commentEditor').summernote('code', '');
            } else {
                $('#commentEditor').val('');
            }
        }).fail(function(xhr){
            alert('Failed to post comment.');
        });
    });
});
</script>
@endsection
