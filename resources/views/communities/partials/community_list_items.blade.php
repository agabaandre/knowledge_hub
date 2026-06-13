@foreach ($communities as $community)
    <div class="col-md-6 col-lg-4 mb-3 communities-list-item"
         data-community-id="{{ $community->id }}"
         data-joined="{{ Auth::check() && ($community->user_joined ?? false) ? 'true' : 'false' }}"
         data-public="{{ ($community->is_public ?? false) ? 'true' : 'false' }}"
         data-search="{{ e(strtolower(strip_tags(($community->community_name ?? '') . ' ' . ($community->description ?? '')))) }}">
        @include('communities.partials.room_card', ['community' => $community, 'pinned' => false])
    </div>
@endforeach
