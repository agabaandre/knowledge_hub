           @auth
            @if(!$row->is_favourite)
            <span class="mr-1">
            <a href="{{ url('publications/add_favourite') }}?id={{ $row->id }}">
                <span class=" mr-2"><i class="fa fa-star mr-1"></i> Add to Favourites </span>
            </a>
            </span>
           @else
            <span class="mr-1">
            <a href="{{ url('publications/remove_favourite') }}?id={{$row->id}}">
                <span class=" mr-2"><i class="fa fa-star mr-1 text-warning"></i> Remove from favourites </span>
            </a>
            </span>
            @endif
        @endauth