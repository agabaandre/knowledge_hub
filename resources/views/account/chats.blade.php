@extends('layouts.plain')

@section('styles')
<style>
    .chats-page-header {
        background: linear-gradient(135deg, var(--theme-color-primary, #006239) 0%, #004d2d 100%);
        color: #fff;
        padding: 1.75rem 0 2rem;
        border-radius: 0 0 12px 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .chats-page-header .chats-title { font-size: 1.5rem; font-weight: 600; margin-bottom: 0.25rem; color: #fff; }
    .chats-page-header .chats-subtitle { font-size: 0.9rem; opacity: 0.92; margin: 0; }
    .chats-doc-group {
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 1.5rem;
        border: 1px solid #e9ecef;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .chats-doc-group .card-header {
        background: #f8fafb;
        border-bottom: 1px solid #e9ecef;
        padding: 1rem 1.25rem;
        font-weight: 600;
        font-size: 1rem;
    }
    .chats-doc-group .card-header .doc-link {
        color: #1a1a1a;
        text-decoration: none;
    }
    .chats-doc-group .card-header .doc-link:hover { color: var(--theme-color-primary, #006239); }
    .chats-doc-group .card-body { padding: 0.75rem 1.25rem 1.25rem; }
    .chats-session-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.85rem 1rem;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        background: #fff;
        border: 1px solid #e9ecef;
        transition: background 0.15s ease, border-color 0.15s ease;
    }
    .chats-session-row:last-child { margin-bottom: 0; }
    .chats-session-row:hover {
        background: #f8fafb;
        border-color: #dee2e6;
    }
    .chats-session-meta { font-size: 0.875rem; color: #5a6c7d; }
    .chats-session-actions { display: flex; gap: 0.5rem; align-items: center; flex-shrink: 0; }
    .chats-session-actions .btn {
        font-weight: 500;
        padding: 0.4rem 0.9rem;
        border-radius: 6px;
    }
    /* Primary buttons use site primary color */
    .chats-doc-group .btn-primary,
    .chats-session-actions .btn-primary {
        background-color: var(--theme-color-primary, #119A48) !important;
        border-color: var(--theme-color-primary, #119A48) !important;
    }
    .chats-doc-group .btn-primary:hover,
    .chats-session-actions .btn-primary:hover {
        filter: brightness(1.08);
    }
    .chats-empty {
        text-align: center;
        padding: 3.5rem 1.5rem;
        color: #5a6c7d;
    }
    .chats-empty .fa-comments { color: #cbd5e0; }
    #confirm-delete-chat-modal .modal-footer .btn { font-weight: 500; padding: 0.5rem 1.25rem; border-radius: 6px; }
</style>
@endsection

@section('content')
<div class="gray">
    <div class="chats-page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="chats-title">My Chats</h1>
                    <p class="chats-subtitle">Chats with PDFs and documents, grouped by resource.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4 pb-5">
        @if (Session::has('alert'))
            <div class="alert alert-{{ Session::get('alert_class', 'info') }} alert-dismissible fade show" role="alert">
                {{ Session::get('alert') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (empty($chatsByDocument))
            <div class="card border-0 shadow-sm">
                <div class="chats-empty">
                    <i class="fa fa-comments fa-3x mb-3"></i>
                    <p class="mb-0 fw-medium">You have no PDF chats yet.</p>
                    <p class="small mt-1 mb-0">Open a resource and use <strong>Khub AI Assistant</strong> to start a conversation.</p>
                </div>
            </div>
        @else
            @foreach ($chatsByDocument as $doc)
                @php
                    $resourceUrl = url('records/resource?id=' . $doc['publication_id']);
                    if (!empty($doc['attachment_id'])) {
                        $resourceUrl .= '&attachment_id=' . $doc['attachment_id'];
                    }
                @endphp
                <div class="chats-doc-group card border-0">
                    <div class="card-header d-flex align-items-center flex-wrap gap-2">
                        <i class="fa fa-file-pdf text-danger me-2"></i>
                        <a href="{{ $resourceUrl }}" class="doc-link flex-grow-1">{{ e($doc['title']) }}</a>
                        <a href="{{ $resourceUrl }}" class="btn btn-sm btn-primary" title="Open resource">
                            <i class="fa fa-external-link-alt me-1"></i> View resource
                        </a>
                    </div>
                    <div class="card-body">
                        @foreach ($doc['sessions'] as $s)
                            <div class="chats-session-row" data-session-id="{{ $s['id'] }}">
                                <div class="chats-session-meta">
                                    <span>{{ $s['message_count'] }} message{{ $s['message_count'] !== 1 ? 's' : '' }}</span>
                                    <span class="mx-2">·</span>
                                    <span>Last activity {{ $s['updated_at']->diffForHumans() }}</span>
                                </div>
                                <div class="chats-session-actions">
                                    <a href="{{ $resourceUrl }}" class="btn btn-sm btn-primary" title="Open chat">
                                        <i class="fa fa-comment-dots me-1"></i> Open chat
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger js-delete-chat" data-session-id="{{ $s['id'] }}" title="Delete this chat">
                                        <i class="fa fa-trash-alt me-1"></i> Delete
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

@if (!empty($chatsByDocument))
<div class="modal fade" id="confirm-delete-chat-modal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteChatLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="confirmDeleteChatLabel">Delete chat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">Are you sure you want to delete this chat? This cannot be undone.</div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-chat-btn"><i class="fa fa-trash-alt me-1"></i> Delete</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
@if (!empty($chatsByDocument))
<script>
(function() {
    var sessionIdToDelete = null;
    var modal = document.getElementById('confirm-delete-chat-modal');
    var confirmBtn = document.getElementById('confirm-delete-chat-btn');
    var deleteUrl = '{{ route("account.chats.delete") }}';
    var csrf = '{{ csrf_token() }}';

    document.querySelectorAll('.js-delete-chat').forEach(function(btn) {
        btn.addEventListener('click', function() {
            sessionIdToDelete = this.getAttribute('data-session-id');
            if (typeof bootstrap !== 'undefined' && modal) {
                var bModal = new bootstrap.Modal(modal);
                bModal.show();
            } else if (typeof $ !== 'undefined' && $.fn.modal) {
                $(modal).modal('show');
            }
        });
    });

    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            if (!sessionIdToDelete) return;
            var idToDelete = sessionIdToDelete;
            sessionIdToDelete = null;
            var formData = new FormData();
            formData.append('_token', csrf);
            formData.append('session_id', idToDelete);
            fetch(deleteUrl, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(function(r) { return r.json().then(function(j) { return { ok: r.ok, json: j }; }); })
                .then(function(result) {
                    if (result.ok && result.json.success) {
                        var row = document.querySelector('.chats-session-row[data-session-id="' + idToDelete + '"]');
                        if (row) {
                            var group = row.closest('.chats-doc-group');
                            row.remove();
                            if (group && group.querySelectorAll('.chats-session-row').length === 0) group.remove();
                        }
                        if (typeof bootstrap !== 'undefined' && modal) bootstrap.Modal.getInstance(modal).hide();
                        else if (typeof $ !== 'undefined') $(modal).modal('hide');
                    } else {
                        alert(result.json.message || 'Failed to delete chat.');
                    }
                })
                .catch(function() { alert('Failed to delete chat. Please try again.'); });
        });
    }
})();
</script>
@endif
@endsection
