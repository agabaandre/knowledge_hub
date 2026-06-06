@php
    $authorCommunities = $authorCommunities ?? collect();
    $primary = settings()->primary_color ?? '#119A48';
@endphp
@if($authorCommunities->isNotEmpty())
<div class="contributor-sidebar-panel contributor-communities-panel mb-4">
    <div class="contributor-sidebar-panel__header">
        <i class="fa fa-users contributor-sidebar-panel__icon" aria-hidden="true"></i>
        <h3 class="contributor-sidebar-panel__title">Communities of practice</h3>
    </div>
    <p class="contributor-sidebar-panel__lead">Member of {{ $authorCommunities->count() }} {{ Str::plural('community', $authorCommunities->count()) }} on the hub.</p>
    <ul class="contributor-communities-list list-unstyled mb-0">
        @foreach($authorCommunities as $community)
            <li class="contributor-communities-item">
                <a href="{{ community_detail_url($community) }}" class="contributor-communities-item__link">
                    <span class="contributor-communities-item__name">{{ $community->community_name }}</span>
                    <span class="contributor-communities-item__meta">
                        <i class="fa fa-users" aria-hidden="true"></i>
                        {{ number_format((int) ($community->approved_members_count ?? $community->members_count ?? 0)) }} members
                    </span>
                </a>
            </li>
        @endforeach
    </ul>
</div>
@endif
