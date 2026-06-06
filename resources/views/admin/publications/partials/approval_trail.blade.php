<div class="ap-card mb-4" id="approval-trail">
    <div class="ap-card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0"><i class="fa fa-history mr-2"></i>Approval Trail</h5>
        <span class="text-muted small">{{ count($approvalTrail) }} event(s)</span>
    </div>
    <div class="ap-card-body">
        @if(count($approvalTrail) === 0)
            <p class="text-muted mb-0">No approval activity has been recorded for this publication yet.</p>
        @else
            <div class="pub-approval-trail">
                @foreach($approvalTrail as $entry)
                    @php
                        $action = $entry->action ?? '';
                        $badgeClass = match ($action) {
                            'approved', 'auto_approved' => 'badge-success',
                            'rejected' => 'badge-danger',
                            'submitted' => 'badge-warning',
                            default => 'badge-secondary',
                        };
                        $label = $entry->action_label ?? ($entry instanceof \App\Models\PublicationApprovalLog ? $entry->actionLabel() : ucfirst(str_replace('_', ' ', $action)));
                        $when = $entry->created_at ? \Carbon\Carbon::parse($entry->created_at)->format('M d, Y H:i') : 'Date unknown';
                        $actor = $entry->performed_by_name ?? 'System';
                        $isLegacy = !empty($entry->is_legacy);
                    @endphp
                    <div class="pub-approval-trail__item">
                        <div class="pub-approval-trail__marker"></div>
                        <div class="pub-approval-trail__content">
                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-1">
                                <div>
                                    <span class="badge {{ $badgeClass }} mr-2">{{ $label }}</span>
                                    @if($isLegacy)
                                        <span class="badge badge-light border">Legacy record</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $when }}</small>
                            </div>
                            <div class="small text-muted mb-1">
                                <strong>By:</strong> {{ $actor }}
                            </div>
                            @if(!empty($entry->reason))
                                <div class="pub-approval-trail__reason">
                                    <strong>Reason:</strong> {{ $entry->reason }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-3 pt-3 border-top small text-muted">
            <strong>Note:</strong> The resource <em>Author</em> is the corporate source or member state credited on the publication.
            The person in the approval trail is the platform user who moderated the submission.
            Older publications may show “moderator not recorded” if they were approved before tracking was enabled.
        </div>
    </div>
</div>

<style>
    .pub-approval-trail {
        position: relative;
        padding-left: 1.25rem;
    }
    .pub-approval-trail__item {
        position: relative;
        padding: 0 0 1.25rem 1rem;
        border-left: 2px solid #e2e8f0;
    }
    .pub-approval-trail__item:last-child {
        padding-bottom: 0;
        border-left-color: transparent;
    }
    .pub-approval-trail__marker {
        position: absolute;
        left: -0.45rem;
        top: 0.25rem;
        width: 0.75rem;
        height: 0.75rem;
        border-radius: 50%;
        background: var(--theme-color-primary, #119A48);
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px #e2e8f0;
    }
    .pub-approval-trail__reason {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 0;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        color: #7f1d1d;
        white-space: pre-wrap;
        word-break: break-word;
    }
</style>
