{{-- Related Resources + Latest (refreshed with AJAX when filters change) --}}
@if(isset($relatedPublications) && $relatedPublications->count() > 0)
<div class="sidebar-content">
  <h5 class="mb-3">Related Resources</h5>
  @foreach($relatedPublications->take(5) as $pub)
  <div class="mb-3 pb-3 border-bottom">
    <a href="{{ publication_url($pub) }}" class="text-decoration-none">
      <h6 class="mb-1" style="font-size:0.9rem;color:#0f172a;line-height:1.4;">{{ Str::limit(strip_tags($pub->title), 80) }}</h6>
    </a>
    <p class="mb-1" style="font-size:0.8rem;color:#64748b;">{{ Str::limit(strip_tags(clean_unicode(publication_description_for_list($pub->description ?? ''))), 100) }}</p>
    <div class="d-flex align-items-center" style="font-size:0.75rem;color:#94a3b8;">
      @if($pub->author)
      <span class="mr-2"><i class="fa fa-user mr-1"></i>
        @if(!empty($pub->author->orcid))
            <a href="https://orcid.org/{{ $pub->author->orcid }}" target="_blank" rel="noopener noreferrer" title="View {{ $pub->author->name }}'s ORCID profile" style="color: inherit; text-decoration: none;">
                {{ $pub->author->name }}
                <i class="fa fa-external-link-alt" style="font-size: 0.65rem; margin-left: 2px;"></i>
            </a>
        @else
            {{ $pub->author->name }}
        @endif
      </span>
      @endif
      <span><i class="fa fa-calendar mr-1"></i>{{ $pub->created_at->format('M Y') }}</span>
    </div>
  </div>
  @endforeach
  <a href="{{ url('records') }}" class="btn btn-sm btn-primary w-100 mt-2">View All Resources</a>
</div>
@endif

@if(isset($latestPublications) && $latestPublications->count() > 0)
<div class="sidebar-content">
  <h5 class="mb-3">Latest Publications</h5>
  @foreach($latestPublications->take(5) as $pub)
  <div class="mb-3 pb-3 border-bottom">
    <a href="{{ publication_url($pub) }}" class="text-decoration-none">
      <h6 class="mb-1" style="font-size:0.9rem;color:#0f172a;line-height:1.4;">{{ Str::limit(strip_tags($pub->title), 80) }}</h6>
    </a>
    <p class="mb-1" style="font-size:0.8rem;color:#64748b;">{{ Str::limit(strip_tags(clean_unicode(publication_description_for_list($pub->description ?? ''))), 100) }}</p>
    <div class="d-flex align-items-center" style="font-size:0.75rem;color:#94a3b8;">
      @if($pub->author)
      <span class="mr-2"><i class="fa fa-user mr-1"></i>
        @if(!empty($pub->author->orcid))
            <a href="https://orcid.org/{{ $pub->author->orcid }}" target="_blank" rel="noopener noreferrer" title="View {{ $pub->author->name }}'s ORCID profile" style="color: inherit; text-decoration: none;">
                {{ $pub->author->name }}
                <i class="fa fa-external-link-alt" style="font-size: 0.65rem; margin-left: 2px;"></i>
            </a>
        @else
            {{ $pub->author->name }}
        @endif
      </span>
      @endif
      <span><i class="fa fa-calendar mr-1"></i>{{ $pub->created_at->format('M Y') }}</span>
    </div>
  </div>
  @endforeach
  <a href="{{ url('records') }}" class="btn btn-sm btn-primary w-100 mt-2">View All Publications</a>
</div>
@endif
