@extends('layouts.plain')

@section('styles')
<style>
    .chats-page-header { background-image: url({{ asset('assets/img/dots.png') }}); background-repeat: repeat-x; background-size: contain; }
    .chats-doc-group { border-radius: 8px; overflow: hidden; margin-bottom: 1.25rem; border: 1px solid #dee2e6; }
    .chats-doc-group .card-header { background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%); border-bottom: 1px solid #dee2e6; padding: 0.85rem 1rem; font-weight: 600; }
    .chats-doc-group .card-body { padding: 0.5rem 1rem 1rem; }
    .chats-session-row { display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.75rem; border-radius: 6px; margin-bottom: 0.35rem; background: #f8f9fa; }
    .chats-session-row:last-child { margin-bottom: 0; }
    .chats-session-row:hover { background: #e9ecef; }
    .chats-session-meta { font-size: 0.875rem; color: #6c757d; }
    .chats-session-actions { display: flex; gap: 0.5rem; align-items: center; }
    .chats-empty { text-align: center; padding: 3rem 1rem; color: #6c757d; }
</style>
@endsection

@section('content')
<div class="gray">
    <div class="bg-light rounded py-5 chats-page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="pl-3">
                                <h3 class="mb-0 ft-medium fs-lg">My Chats</h3>
                                <p class="mb-0 text-muted small mt-1">Chats with PDFs and documents, grouped by resource.</p>
                            </div>
                        </div>
                    </div>
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
                    <i class="fa fa-comments fa-3x text-muted mb-3"></i>
                    <p class="mb-0">You have no PDF chats yet.</p>
                    <p class="small mt-1">Open a resource and use <strong>Chat with PDF</strong> to start a conversation.</p>
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
                <div class="chats-doc-group card border-0 shadow-sm">
                    <div class="card-header d-flex align-items-center">
                        <i class="fa fa-file-pdf text-danger mr-2"></i>
                        <a href="{{ $resourceUrl }}" class="text-dark text-decoration-none">{{ e($doc['title']) }}</a>
                        <a href="{{ $resourceUrl }}" class="btn btn-sm btn-outline-primary ml-auto" title="Open resource">View resource</a>
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
                                    <a href="{{ $resourceUrl }}" class="btn btn-sm btn-outline-primary" title="Open chat">Open chat</a>
                                    <button type="button" class="btn btn-sm btn-outline-danger js-delete-chat" data-session-id="{{ $s['id'] }}" title="Delete this chat">
                                        <i class="fa fa-trash"></i> Delete
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
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteChatLabel">Delete chat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">Are you sure you want to delete this chat? This cannot be undone.</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-chat-btn">Delete</button>
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
