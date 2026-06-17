@php
    /** @var \App\Models\Publication|object $publication */
    $publication = $publication ?? $row ?? null;
    if (! $publication) {
        return;
    }
    $khubAiConfig = method_exists($publication, 'defaultKhubAiConfig')
        ? $publication->defaultKhubAiConfig()
        : ['assistant_mode' => 'publication', 'attachment_id' => null];
    $defaultAssistantMode = $khubAiConfig['assistant_mode'] ?? 'publication';
    $defaultAssistantAttachmentId = $khubAiConfig['attachment_id'] ?? null;
    $docTitle = \Illuminate\Support\Str::limit(strip_tags($publication->title ?? 'Document'), 200);
    $btnClass = $btnClass ?? 'btn btn-sm btn-primary';
    $btnStyle = $btnStyle ?? 'background-color: var(--theme-color-primary, #119A48); border: none; color: white; text-decoration: none; padding: 0.375rem 0.75rem; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 500;';
@endphp
@auth
    <button type="button"
            class="{{ $btnClass }} js-open-pdf-chat"
            data-publication-id="{{ $publication->id }}"
            data-attachment-id="{{ $defaultAssistantAttachmentId !== null ? $defaultAssistantAttachmentId : '' }}"
            data-assistant-mode="{{ $defaultAssistantMode }}"
            data-doc-title="{{ e($docTitle) }}"
            style="{{ $btnStyle }}"
            onclick="event.preventDefault();event.stopPropagation();if(typeof openPdfChat==='function'){openPdfChat({{ (int) $publication->id }}, @json($defaultAssistantAttachmentId), @json($docTitle), @json($defaultAssistantMode));}else{window.location.href=@json(publication_url($publication));}">
        <i class="fa-solid fa-microchip"></i> Khub AI
    </button>
@else
    <a href="{{ url('login') }}?redirect={{ urlencode(request()->fullUrl()) }}"
       class="{{ $btnClass }}"
       style="{{ $btnStyle }}"
       onclick="event.stopPropagation();">
        <i class="fa-solid fa-microchip"></i> Khub AI <small>(login)</small>
    </a>
@endauth
