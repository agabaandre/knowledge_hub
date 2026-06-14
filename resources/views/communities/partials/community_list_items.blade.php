@foreach ($communities as $community)
    <div class="communities-card-grid__item communities-list-item"
         data-community-id="{{ $community->id }}"
         data-joined="{{ Auth::check() && ($community->user_joined ?? false) ? 'true' : 'false' }}"
         data-public="{{ ($community->is_public ?? false) ? 'true' : 'false' }}"
         data-search="{{ e(strtolower(strip_tags(
             ($community->community_name ?? '') . ' ' .
             ($community->description ?? '') . ' ' .
             collect($community->listing_chairs ?? [])->map(fn ($c) => ($c['user']->name ?? '') . ' ' . ($c['job_title'] ?? ''))->implode(' ')
         ))) }}">
        @include('communities.partials.room_card', ['community' => $community, 'pinned' => false])
    </div>
@endforeach
