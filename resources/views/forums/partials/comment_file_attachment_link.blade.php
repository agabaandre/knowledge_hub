@php
    $rawExt = forum_comment_attachment_raw_extension($attachment);
    $fileHref = forum_comment_attachment_effective_href($attachment);
    $previewExt = forum_comment_attachment_preview_extension($attachment);
    $isVideo = in_array($rawExt, ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'm4v', 'ogg', 'ogv']);
    $isPdf = $rawExt === 'pdf';
    $isWord = in_array($rawExt, ['doc', 'docx']);
    $isExcel = in_array($rawExt, ['xls', 'xlsx']);
    $isPowerpoint = in_array($rawExt, ['ppt', 'pptx']);
    $isImage = in_array($rawExt, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
    $isOfficeToPdf = forum_comment_attachment_is_convertible_office($attachment);
    $fileName = $attachment->name ?? basename($attachment->path);
    $canPreview = $isPdf || $isVideo || $isOfficeToPdf;
    $iconClass = 'fa-file-o';
    if ($isPdf) {
        $iconClass = 'fa-file-pdf-o';
    } elseif ($isVideo) {
        $iconClass = 'fa-file-video-o';
    } elseif ($isWord) {
        $iconClass = 'fa-file-word-o';
    } elseif ($isExcel) {
        $iconClass = 'fa-file-excel-o';
    } elseif ($isPowerpoint) {
        $iconClass = 'fa-file-powerpoint-o';
    } elseif ($isImage) {
        $iconClass = 'fa-file-image-o';
    }
@endphp
<a href="{{ $fileHref }}" target="_blank" class="comment-attachment-file"
   style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 8px; border-radius: 4px; text-decoration: none; color: #374151; font-size: 0.8125rem; transition: background 0.2s;"
   onmouseover="this.style.background='#f3f4f6'"
   onmouseout="this.style.background='transparent'"
   @if($canPreview) onclick="event.preventDefault(); previewFile(@json($fileHref), @json($previewExt)); return false;" @endif>
    <i class="fa {{ $iconClass }}" style="font-size: 0.875rem; color: {{ $isPdf ? '#dc2626' : ($isVideo ? '#3b82f6' : ($isWord ? '#2563eb' : ($isExcel ? '#16a34a' : ($isPowerpoint ? '#ea580c' : '#6b7280')))) }};"></i>
    <span style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $fileName }}</span>
    @if($canPreview)
    <i class="fa fa-eye" style="font-size: 0.75rem; opacity: 0.7; margin-left: 4px;" title="Click to preview"></i>
    @endif
</a>
