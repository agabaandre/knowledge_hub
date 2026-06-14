@php
    $publication = $row ?? $publication ?? null;
    $attachments = collect($publication->attachments ?? []);
    $maxVisible = 4;
    $visible = $attachments->take($maxVisible);
    $overflow = max(0, $attachments->count() - $maxVisible);
    $attachmentCount = $attachments->count();
    $attachmentPanelId = 'pub-attachments-' . (int) ($publication->id ?? 0);
@endphp
@if($attachments->isNotEmpty())
<details class="community-pub-attachments">
    <summary class="community-pub-attachments__toggle" aria-controls="{{ $attachmentPanelId }}">
        <i class="fa fa-paperclip mr-1" aria-hidden="true"></i>
        <span class="community-pub-attachments__toggle-show">Show attachments ({{ $attachmentCount }})</span>
        <span class="community-pub-attachments__toggle-hide">Hide attachments</span>
        <i class="fa fa-chevron-down community-pub-attachments__chevron" aria-hidden="true"></i>
    </summary>
    <div class="community-pub-attachments__panel" id="{{ $attachmentPanelId }}">
        <div class="community-pub-attachments__strip">
            @foreach($visible as $file)
                @php
                    $url = $file->file;
                    $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION)) ?: 'pdf';
                    $ext = function_exists('normalize_publication_attachment_extension')
                        ? normalize_publication_attachment_extension($ext)
                        : $ext;
                    $office = in_array($ext, ['ppt','pptx','doc','docx','xls','xlsx'], true) ? 1 : 0;
                    $humanReadable = trim((string) ($file->original_filename ?? $file->description ?? $file->download_filename ?? 'File'));
                    $humanReadable = str_replace('_', ' ', $humanReadable);
                    $displayName = Str::limit($humanReadable, 42);
                    $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp','svg'], true);
                    $isPdf = $ext === 'pdf' || !empty($file->is_pdf);
                    $fileIcon = 'fa-file';
                    if ($isPdf) {
                        $fileIcon = 'fa-file-pdf';
                    } elseif (in_array($ext, ['doc','docx'], true)) {
                        $fileIcon = 'fa-file-word';
                    } elseif (in_array($ext, ['xls','xlsx'], true)) {
                        $fileIcon = 'fa-file-excel';
                    } elseif (in_array($ext, ['ppt','pptx'], true)) {
                        $fileIcon = 'fa-file-powerpoint';
                    } elseif ($isImage) {
                        $fileIcon = 'fa-file-image';
                    } elseif (in_array($ext, ['mp4','avi','mov','webm','mkv'], true)) {
                        $fileIcon = 'fa-file-video';
                    } elseif (in_array($ext, ['mp3','wav','ogg','aac','m4a'], true)) {
                        $fileIcon = 'fa-file-audio';
                    }
                @endphp
                <div class="community-pub-attachment-chip{{ $isImage ? ' community-pub-attachment-chip--image' : '' }}">
                    @if($isImage)
                        <button type="button" class="community-pub-attachment-thumb preview-attachment"
                                data-file-url="{{ $url }}"
                                data-file-ext="{{ $ext }}"
                                data-file-office="0"
                                onclick="window.previewAttachmentClick && window.previewAttachmentClick(event, this); return false;"
                                title="Preview {{ $displayName }}">
                            <img src="{{ $url }}" alt="{{ $displayName }}" loading="lazy"
                                 onerror="this.closest('.community-pub-attachment-chip').classList.remove('community-pub-attachment-chip--image');">
                        </button>
                    @else
                        <span class="community-pub-attachment-chip__icon community-pub-attachment-chip__icon--{{ $isPdf ? 'pdf' : 'file' }}">
                            <i class="fa {{ $fileIcon }}" aria-hidden="true"></i>
                        </span>
                    @endif
                    <div class="community-pub-attachment-chip__body">
                        <span class="community-pub-attachment-chip__name" title="{{ $humanReadable }}">{{ $displayName }}</span>
                        <div class="community-pub-attachment-chip__actions">
                            @if($isPdf || $isImage || $office)
                                <button type="button" class="btn btn-link btn-sm p-0 preview-attachment"
                                        data-file-url="{{ $url }}"
                                        data-file-ext="{{ $ext }}"
                                        data-file-office="{{ $office }}"
                                        onclick="window.previewAttachmentClick && window.previewAttachmentClick(event, this); return false;">
                                    Preview
                                </button>
                            @endif
                            <a href="{{ $url }}" class="btn btn-link btn-sm p-0" download target="_blank" rel="noopener">Download</a>
                        </div>
                    </div>
                </div>
            @endforeach
            @if($overflow > 0)
                <a href="{{ publication_url($publication) }}" class="community-pub-attachment-more">
                    +{{ $overflow }} more
                </a>
            @endif
        </div>
    </div>
</details>
@endif
