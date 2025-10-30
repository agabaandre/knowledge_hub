@extends('layouts.plain')

@section('styles')
<style>
    .event-hero{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;box-shadow:0 4px 14px rgba(0,0,0,.06);border-top:3px solid var(--theme-color-primary, #119A48)}
    .event-banner{height:280px;background:#f8fafc;display:flex;align-items:center;justify-content:center;overflow:hidden}
    .event-banner img{width:100%;height:100%;object-fit:cover}
    .event-body{padding:20px}
    .event-title{margin:0 0 8px;font-weight:800;color:#0f172a}
    .event-meta{display:flex;flex-wrap:wrap;gap:14px;color:#475569;margin-bottom:12px}
    .event-meta .item{font-size:.95rem}
    .event-actions{display:flex;gap:10px;margin-top:12px}
    .event-actions .btn{border-radius:8px}
    .event-actions .btn-success{background:var(--theme-color-primary, #119A48);border-color:var(--theme-color-primary, #119A48)}
    .event-actions .btn-success:hover{filter:brightness(0.95)}
    .event-actions .btn-outline-primary{color:var(--theme-color-primary, #119A48);border-color:var(--theme-color-primary, #119A48)}
    .event-actions .btn-outline-primary:hover{background:var(--theme-color-primary, #119A48);color:#fff}
    /* Apply theme color to sidebar buttons as well */
    .event-content .btn-success{background:var(--theme-color-primary, #119A48);border-color:var(--theme-color-primary, #119A48)}
    .event-content .btn-success:hover{filter:brightness(0.95)}
    .event-content .btn-outline-primary{color:var(--theme-color-primary, #119A48);border-color:var(--theme-color-primary, #119A48)}
    .event-content .btn-outline-primary:hover{background:var(--theme-color-primary, #119A48);color:#fff}
    .event-content{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:18px;box-shadow:0 2px 8px rgba(0,0,0,.04)}
    .event-content h5{color:var(--theme-color-primary, #119A48)}
    .related-event-link:hover{background:#f8fafc;border-color:var(--theme-color-primary, #119A48)!important;transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.08)}
    .event-search-form .input-group-append{margin-left:5px}
    .event-search-form .btn{white-space:nowrap}
    @media(max-width:768px){.event-banner{height:200px}}
</style>
@endsection

@section('content')
<div class="py-4 gray">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <div class="event-hero mb-3">
          <div class="event-banner">
            <img src="{{ $event->banner_image ? asset($event->banner_image) : asset('assets/images/cover.png') }}" alt="{{ $event->title }}" onerror="this.onerror=null;this.src='{{ asset('assets/images/cover.png') }}'" />
          </div>
          <div class="event-body">
            <h2 class="event-title">{{ $event->title }}</h2>
            @php
              $start = $event->startdate ? \Carbon\Carbon::parse($event->startdate)->format('M d, Y') : '';
              $end   = $event->enddate ? \Carbon\Carbon::parse($event->enddate)->format('M d, Y') : '';
              $range = trim($start . ($end? ' - ' . $end : ''));
              $isPast = $event->enddate
                  ? \Carbon\Carbon::parse($event->enddate)->isPast()
                  : ($event->startdate ? \Carbon\Carbon::parse($event->startdate)->isPast() : false);
            @endphp
            <div class="event-meta">
              @if($range)
              <div class="item"><i class="fa fa-clock-o mr-1"></i>{{ $range }}</div>
              @endif
              @if($event->venue)
              <div class="item"><i class="fa fa-map-marker mr-1"></i>{{ $event->venue }}</div>
              @endif
              @if(!empty($event->organized_by))
              <div class="item"><i class="fa fa-building mr-1"></i>{{ $event->organized_by }}</div>
              @endif
              @if(!empty($event->fee))
              <div class="item"><i class="fa fa-ticket mr-1"></i>{{ $event->fee }}</div>
              @endif
            </div>
            <div class="event-actions">
              @if(!$isPast && $event->registration_link)
              <a href="{{ $event->registration_link }}" target="_blank" class="btn btn-success"><i class="fa fa-edit mr-1"></i>Register</a>
              @endif
              @if($event->event_link)
              <a href="{{ $event->event_link }}" target="_blank" class="btn btn-outline-primary"><i class="fa fa-external-link mr-1"></i>Visit Event Site</a>
              @endif
            </div>
          </div>
        </div>

        <div class="event-content mb-3">
          {!! $event->description !!}
        </div>

        {{-- Search Events --}}
        <div class="event-content mb-3">
          <h5 class="mb-3">Search Events</h5>
          <div class="spotlight px-0 py-3" style="background-color: transparent;">
            <form action="{{ url('events/'.$event->id) }}" class="filters" role="search" aria-label="Search events">
              <div class="row no-gutters bg-white rounded search-form" id="simple_search" style="border-radius: 0.375rem !important;">
                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-12">
                  <div class="form-group mb-0 position-relative main_search">
                    <label for="main-search-events" class="sr-only">Search Events</label>
                    <input type="text" id="main-search-events" class="form-control left-ico term main-search"
                           name="term" value="{{ $searchTerm ?? '' }}" placeholder="Type Keywords to search events..." />
                  </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 bg-show">
                  <div class="form-group mb-0 position-relative">
                    <button class="btn full-width theme-bg text-white fs-md py-3" type="submit">
                      <i class="fa fa-magnifying-glass"></i> Search
                    </button>
                  </div>
                </div>
              </div>
              <div class="col-md-4 sm-show mt-1 d-md-none">
                <button class="btn full-width theme-bg text-white fs-md py-3" type="submit">
                  <i class="fa fa-magnifying-glass"></i> Search
                </button>
              </div>
            </form>
          </div>
        </div>

        {{-- Search Results --}}
        @if(isset($searchResults) && $searchResults->count() > 0)
        <div class="event-content mb-3">
          <h5 class="mb-3">Search Results ({{ $searchResults->total() }})</h5>
          <div class="row">
            @foreach($searchResults as $result)
            @php
              $resStart = $result->startdate ? \Carbon\Carbon::parse($result->startdate)->format('M d, Y') : '';
              $resEnd   = $result->enddate ? \Carbon\Carbon::parse($result->enddate)->format('M d, Y') : '';
              $resRange = trim($resStart . ($resEnd? ' - ' . $resEnd : ''));
            @endphp
            <div class="col-12 mb-3">
              <a href="{{ url('events/'.$result->id) }}" class="text-decoration-none related-event-link" style="display:block;padding:12px;border:1px solid #e2e8f0;border-radius:8px;transition:all 0.2s ease;">
                <h6 style="font-size:0.95rem;color:#0f172a;margin-bottom:6px;">{{ Str::limit($result->title, 80) }}</h6>
                <p style="font-size:0.85rem;color:#64748b;margin-bottom:8px;">{{ Str::limit(strip_tags($result->description ?? ''), 120) }}</p>
                <div style="font-size:0.8rem;color:#64748b;">
                  @if($resRange)
                  <span><i class="fa fa-calendar mr-1"></i>{{ $resRange }}</span>
                  @endif
                  @if($result->venue)
                  <span class="ml-2"><i class="fa fa-map-marker mr-1"></i>{{ Str::limit($result->venue, 30) }}</span>
                  @endif
                  @if($result->organized_by)
                  <span class="ml-2"><i class="fa fa-building mr-1"></i>{{ Str::limit($result->organized_by, 25) }}</span>
                  @endif
                </div>
              </a>
            </div>
            @endforeach
          </div>
          
          {{-- Pagination --}}
          @if($searchResults->hasPages())
          <div class="mt-3">
            {{ $searchResults->links() }}
          </div>
          @endif
        </div>
        @elseif(isset($searchTerm) && $searchTerm)
        <div class="event-content mb-3">
          <div class="alert alert-info mb-0">
            <i class="fa fa-info-circle mr-2"></i>No events found matching "{{ $searchTerm }}"
          </div>
        </div>
        @endif

        {{-- Related Past Events --}}
        @if(isset($relatedEvents) && $relatedEvents->count() > 0)
        <div class="event-content">
          <h5 class="mb-3">Related Past Events</h5>
          <div class="row">
            @foreach($relatedEvents as $relatedEvent)
            @php
              $relStart = $relatedEvent->startdate ? \Carbon\Carbon::parse($relatedEvent->startdate)->format('M d, Y') : '';
              $relEnd   = $relatedEvent->enddate ? \Carbon\Carbon::parse($relatedEvent->enddate)->format('M d, Y') : '';
              $relRange = trim($relStart . ($relEnd? ' - ' . $relEnd : ''));
            @endphp
            <div class="col-12 mb-3">
              <a href="{{ url('events/'.$relatedEvent->id) }}" class="text-decoration-none related-event-link" style="display:block;padding:12px;border:1px solid #e2e8f0;border-radius:8px;transition:all 0.2s ease;">
                <h6 style="font-size:0.95rem;color:#0f172a;margin-bottom:6px;">{{ Str::limit($relatedEvent->title, 60) }}</h6>
                <div style="font-size:0.8rem;color:#64748b;">
                  @if($relRange)
                  <span><i class="fa fa-calendar mr-1"></i>{{ $relRange }}</span>
                  @endif
                  @if($relatedEvent->venue)
                  <span class="ml-2"><i class="fa fa-map-marker mr-1"></i>{{ Str::limit($relatedEvent->venue, 30) }}</span>
                  @endif
                </div>
              </a>
            </div>
            @endforeach
          </div>
        </div>
        @endif
      </div>
      <div class="col-lg-4">
        <div class="event-content mb-3">
          <h5 class="mb-2">Event Summary</h5>
          <ul class="list-unstyled mb-0">
            @if($range)
            <li class="mb-2"><i class="fa fa-calendar mr-2"></i>{{ $range }}</li>
            @endif
            @if($event->venue)
            <li class="mb-2"><i class="fa fa-map-marker mr-2"></i>{{ $event->venue }}</li>
            @endif
            @if(!empty($event->organized_by))
            <li class="mb-2"><i class="fa fa-building mr-2"></i>{{ $event->organized_by }}</li>
            @endif
            @if(!empty($event->contact_person))
            <li class="mb-2"><i class="fa fa-user mr-2"></i>{{ $event->contact_person }}</li>
            @endif
            @if(!empty($event->fee))
            <li class="mb-2"><i class="fa fa-ticket mr-2"></i>{{ $event->fee }}</li>
            @endif
          </ul>
          <div class="mt-3">
            @if(!$isPast && $event->registration_link)
            <a href="{{ $event->registration_link }}" target="_blank" class="btn btn-success btn-block"><i class="fa fa-edit mr-1"></i>Register</a>
            @endif
            @if($event->event_link)
            <a href="{{ $event->event_link }}" target="_blank" class="btn btn-outline-primary btn-block"><i class="fa fa-external-link mr-1"></i>Event Website</a>
            @endif
          </div>
        </div>

        {{-- Related Resources --}}
        @if(isset($relatedPublications) && $relatedPublications->count() > 0)
        <div class="event-content mb-3">
          <h5 class="mb-3">Related Resources</h5>
          @foreach($relatedPublications->take(5) as $pub)
          <div class="mb-3 pb-3 border-bottom">
            <a href="{{ url('records/resource?id=' . $pub->id) }}" class="text-decoration-none">
              <h6 class="mb-1" style="font-size:0.9rem;color:#0f172a;line-height:1.4;">{{ Str::limit(strip_tags($pub->title), 80) }}</h6>
            </a>
            <p class="mb-1" style="font-size:0.8rem;color:#64748b;">{{ Str::limit(strip_tags($pub->description ?? ''), 100) }}</p>
            <div class="d-flex align-items-center" style="font-size:0.75rem;color:#94a3b8;">
              @if($pub->author)
              <span class="mr-2"><i class="fa fa-user mr-1"></i>{{ $pub->author->name }}</span>
              @endif
              <span><i class="fa fa-calendar mr-1"></i>{{ $pub->created_at->format('M Y') }}</span>
            </div>
          </div>
          @endforeach
          <a href="{{ url('records') }}" class="btn btn-sm btn-outline-primary btn-block mt-2">View All Resources</a>
        </div>
        @endif

        {{-- Latest Publications --}}
        @if(isset($latestPublications) && $latestPublications->count() > 0)
        <div class="event-content">
          <h5 class="mb-3">Latest Publications</h5>
          @foreach($latestPublications->take(5) as $pub)
          <div class="mb-3 pb-3 border-bottom">
            <a href="{{ url('records/resource?id=' . $pub->id) }}" class="text-decoration-none">
              <h6 class="mb-1" style="font-size:0.9rem;color:#0f172a;line-height:1.4;">{{ Str::limit(strip_tags($pub->title), 80) }}</h6>
            </a>
            <p class="mb-1" style="font-size:0.8rem;color:#64748b;">{{ Str::limit(strip_tags($pub->description ?? ''), 100) }}</p>
            <div class="d-flex align-items-center" style="font-size:0.75rem;color:#94a3b8;">
              @if($pub->author)
              <span class="mr-2"><i class="fa fa-user mr-1"></i>{{ $pub->author->name }}</span>
              @endif
              <span><i class="fa fa-calendar mr-1"></i>{{ $pub->created_at->format('M Y') }}</span>
            </div>
          </div>
          @endforeach
          <a href="{{ url('records') }}" class="btn btn-sm btn-outline-primary btn-block mt-2">View All Publications</a>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection


