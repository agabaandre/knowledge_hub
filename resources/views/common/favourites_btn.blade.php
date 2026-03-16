        @auth
            <span class="mr-1">
            <button type="button"
                class="btn btn-sm btn-outline-danger js-favourite-pub-btn"
                data-publication-id="{{ $row->id }}"
                data-favourited="{{ $row->is_favourite ? '1' : '0' }}"
                style="border-color: #ef4444; color: #ef4444; background-color: transparent; text-decoration: none; padding: 0.375rem 0.75rem; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 500; cursor: pointer;"
                onclick="if(window.handlePubFavourite){event.preventDefault();event.stopPropagation();window.handlePubFavourite(this);}">
                <i class="fa fa-heart{{ $row->is_favourite ? '' : '-o' }} mr-1" style="{{ $row->is_favourite ? 'color: #ef4444;' : 'color: inherit;' }}"></i>
                <span class="js-fav-label">{{ $row->is_favourite ? 'Favorite' : 'Add favorite' }}</span>
            </button>
            </span>
        @else
           <span class="mr-1">
            <a href="{{ url('login') }}" class="btn btn-sm btn-outline-danger" style="border-color: #ef4444; color: #ef4444; text-decoration: none; padding: 0.375rem 0.75rem; border-radius: 0.25rem; font-size: 0.875rem;">
                <i class="fa fa-heart-o mr-1"></i> Add favorite
            </a>
            </span>
        @endauth