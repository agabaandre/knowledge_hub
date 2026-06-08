@extends('layouts.plain')

@php
    $primary = settings()->primary_color ?? '#119A48';
    $totalResources = count($chatsByDocument ?? []);
    $totalSessions = 0;
    foreach ($chatsByDocument ?? [] as $doc) {
        $totalSessions += count($doc['sessions'] ?? []);
    }
@endphp

@section('styles')
<style>
    .my-chats-page {
        --mc-primary: {{ $primary }};
        background: #f4f5f7;
        padding: 2rem 0 2.5rem;
    }
    .my-chats-page,
    .my-chats-page * {
        border-radius: 0 !important;
    }
    .my-chats-shell {
        border: 1px solid #e2e8f0;
        background: #fff;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }
    .my-chats-shell > .card-header {
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        padding: 1.35rem 1.5rem 1.15rem;
    }
    .my-chats-shell__title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 0.35rem;
    }
    .my-chats-shell__subtitle {
        font-size: 0.9rem;
        color: #64748b;
        margin: 0;
        max-width: 42rem;
        line-height: 1.5;
    }
    .my-chats-quick-links {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    .my-chats-quick-links a {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 0.85rem;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #334155;
        font-size: 0.8125rem;
        font-weight: 600;
        text-decoration: none;
        transition: border-color 0.15s ease, color 0.15s ease;
    }
    .my-chats-quick-links a:hover,
    .my-chats-quick-links a.is-active {
        border-color: var(--mc-primary);
        color: var(--mc-primary);
        text-decoration: none;
    }
    .my-chats-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #f1f5f9;
    }
    .my-chats-stat__value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.1;
    }
    .my-chats-stat__label {
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #64748b;
        margin-top: 0.15rem;
    }
    .my-chats-shell > .card-body {
        padding: 0;
    }
    .my-chats-resource {
        border-bottom: 1px solid #e2e8f0;
    }
    .my-chats-resource:last-child {
        border-bottom: none;
    }
    .my-chats-resource__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        padding: 1rem 1.5rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .my-chats-resource__title-wrap {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        min-width: 0;
        flex: 1;
    }
    .my-chats-resource__icon {
        width: 2rem;
        height: 2rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        border: 1px solid #e2e8f0;
        color: #dc2626;
        flex-shrink: 0;
        margin-top: 0.1rem;
    }
    .my-chats-resource__title {
        font-size: 0.98rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.4;
        margin: 0;
    }
    .my-chats-resource__meta {
        font-size: 0.78rem;
        color: #64748b;
        margin-top: 0.2rem;
    }
    .my-chats-table {
        width: 100%;
        margin: 0;
        font-size: 0.875rem;
    }
    .my-chats-table thead th {
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 0.7rem 1rem;
        white-space: nowrap;
    }
    .my-chats-table tbody td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }
    .my-chats-table tbody tr:last-child td {
        border-bottom: none;
    }
    .my-chats-table tbody tr:hover td {
        background: #fafbfc;
    }
    .my-chats-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
        justify-content: flex-end;
    }
    .my-chats-actions .btn {
        font-size: 0.8125rem;
        font-weight: 600;
        padding: 0.38rem 0.8rem;
    }
    .my-chats-actions .btn-primary {
        background-color: var(--mc-primary) !important;
        border-color: var(--mc-primary) !important;
    }
    .my-chats-empty {
        text-align: center;
        padding: 3rem 1.5rem 3.5rem;
        color: #64748b;
    }
    .my-chats-empty__icon {
        width: 3rem;
        height: 3rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #94a3b8;
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }
    .my-chats-empty h2 {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.5rem;
    }
    .my-chats-empty p {
        margin: 0;
        max-width: 28rem;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.55;
    }
    @media (max-width: 767.98px) {
        .my-chats-table thead {
            display: none;
        }
        .my-chats-table tbody tr {
            display: block;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.85rem 1rem;
        }
        .my-chats-table tbody tr:last-child {
            border-bottom: none;
        }
        .my-chats-table tbody td {
            display: block;
            border: none;
            padding: 0.2rem 0;
        }
        .my-chats-table tbody td::before {
            content: attr(data-label);
            display: block;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 0.15rem;
        }
        .my-chats-table tbody td.my-chats-actions {
            margin-top: 0.65rem;
            padding-top: 0.65rem;
            border-top: 1px solid #f1f5f9;
        }
        .my-chats-table tbody td.my-chats-actions::before {
            display: none;
        }
        .my-chats-actions {
            justify-content: flex-start;
        }
    }
</style>
@endsection

@section('content')
<section class="middle my-chats-page">
    <div class="container">
        <div class="card my-chats-shell border-0">
            <div class="card-header">
                <h1 class="my-chats-shell__title">My Khub AI chats</h1>
                <p class="my-chats-shell__subtitle">
                    Conversations with Khub AI on publications and documents, grouped by resource.
                </p>

                <nav class="my-chats-quick-links" aria-label="Account shortcuts">
                    <a href="{{ route('account.profile') }}"><i class="fa fa-user" aria-hidden="true"></i> Profile</a>
                    <a href="{{ route('account.publications') }}"><i class="fa fa-file-lines" aria-hidden="true"></i> Publications</a>
                    <a href="{{ route('account.my-forums') }}"><i class="fa fa-comments" aria-hidden="true"></i> Forums</a>
                    <a href="{{ route('account.favourites') }}"><i class="fa fa-star" aria-hidden="true"></i> Favourites</a>
                    <a href="{{ route('account.chats') }}" class="is-active"><i class="fa fa-microchip" aria-hidden="true"></i> Khub AI chats</a>
                </nav>

                @if (!empty($chatsByDocument))
                <div class="my-chats-stats">
                    <div>
                        <div class="my-chats-stat__value">{{ $totalResources }}</div>
                        <div class="my-chats-stat__label">{{ $totalResources === 1 ? 'Resource' : 'Resources' }}</div>
                    </div>
                    <div>
                        <div class="my-chats-stat__value">{{ $totalSessions }}</div>
                        <div class="my-chats-stat__label">{{ $totalSessions === 1 ? 'Conversation' : 'Conversations' }}</div>
                    </div>
                </div>
                @endif
            </div>

            <div class="card-body">
                @if (Session::has('alert'))
                    <div class="alert alert-{{ Session::get('alert_class', 'info') }} alert-dismissible fade show m-3 mb-0" role="alert">
                        {{ Session::get('alert') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (empty($chatsByDocument))
                    <div class="my-chats-empty">
                        <div class="my-chats-empty__icon"><i class="fa fa-microchip" aria-hidden="true"></i></div>
                        <h2>No Khub AI chats yet</h2>
                        <p>Open a publication and use <strong>Khub AI</strong> to ask questions about the resource. Your conversations will appear here.</p>
                        <a href="{{ url('records') }}" class="btn btn-primary mt-3">Browse resources</a>
                    </div>
                @else
                    @foreach ($chatsByDocument as $doc)
                        @php
                            $resourceUrl = publication_url($doc['publication_id']);
                            if (!empty($doc['attachment_id'])) {
                                $resourceUrl .= '&attachment_id=' . $doc['attachment_id'];
                            }
                            $sessionCount = count($doc['sessions'] ?? []);
                        @endphp
                        <section class="my-chats-resource" aria-labelledby="chat-resource-{{ $doc['publication_id'] }}-{{ $doc['attachment_id'] ?? 'main' }}">
                            <div class="my-chats-resource__head">
                                <div class="my-chats-resource__title-wrap">
                                    <span class="my-chats-resource__icon" aria-hidden="true"><i class="fa fa-file-pdf"></i></span>
                                    <div class="min-w-0">
                                        <h2 class="my-chats-resource__title" id="chat-resource-{{ $doc['publication_id'] }}-{{ $doc['attachment_id'] ?? 'main' }}">
                                            <a href="{{ $resourceUrl }}" class="text-decoration-none text-dark">{{ e($doc['title']) }}</a>
                                        </h2>
                                        <div class="my-chats-resource__meta">
                                            {{ $sessionCount }} {{ $sessionCount === 1 ? 'conversation' : 'conversations' }}
                                            @if (!empty($doc['attachment_id']))
                                                · Attachment chat
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ $resourceUrl }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa fa-external-link-alt me-1" aria-hidden="true"></i> Open resource
                                </a>
                            </div>

                            <div class="table-responsive">
                                <table class="table my-chats-table mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">Started</th>
                                            <th scope="col">Messages</th>
                                            <th scope="col">Last activity</th>
                                            <th scope="col" class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($doc['sessions'] as $s)
                                            <tr class="js-chat-session-row" data-session-id="{{ $s['id'] }}">
                                                <td data-label="Started">{{ $s['created_at']->format('M j, Y g:i A') }}</td>
                                                <td data-label="Messages">{{ $s['message_count'] }}</td>
                                                <td data-label="Last activity">{{ $s['updated_at']->diffForHumans() }}</td>
                                                <td class="my-chats-actions text-end" data-label="Actions">
                                                    <a href="{{ $resourceUrl }}" class="btn btn-sm btn-primary">
                                                        <i class="fa fa-comment-dots me-1" aria-hidden="true"></i> Open chat
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger js-delete-chat" data-session-id="{{ $s['id'] }}">
                                                        <i class="fa fa-trash-alt me-1" aria-hidden="true"></i> Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</section>

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
                bootstrap.Modal.getOrCreateInstance(modal).show();
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
                        var row = document.querySelector('.js-chat-session-row[data-session-id="' + idToDelete + '"]');
                        if (row) {
                            var resource = row.closest('.my-chats-resource');
                            row.remove();
                            if (resource && resource.querySelectorAll('.js-chat-session-row').length === 0) {
                                resource.remove();
                            }
                        }
                        if (document.querySelectorAll('.js-chat-session-row').length === 0) {
                            window.location.reload();
                        }
                        if (typeof bootstrap !== 'undefined' && modal) {
                            bootstrap.Modal.getInstance(modal)?.hide();
                        } else if (typeof $ !== 'undefined') {
                            $(modal).modal('hide');
                        }
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
