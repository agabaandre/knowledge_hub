@if(isset($events) && count($events))
<style>
    .events-strip{margin:16px auto 8px;border:1px solid #e2e8f0;border-radius:0.25rem;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,.06);overflow:hidden}
    .events-strip .head{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-bottom:1px solid #eef2f7}
    .events-strip .head h5{margin:0;font-weight:700;color:#0f172a}
    .events-track{display:flex;overflow-x:auto;scroll-snap-type:x mandatory;gap:12px;padding:12px;scroll-behavior:smooth}
    .events-track::-webkit-scrollbar{height:8px}
    .events-track::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:0.25rem}
    .event-card{min-width:520px;max-width:560px;flex:0 0 auto;border:1px solid #e2e8f0;border-radius:0.25rem;scroll-snap-align:start;background:#fff;display:flex;overflow:hidden}
    .event-cover{width:42%;min-width:42%;height:170px;background:#f8fafc;border-right:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;overflow:hidden}
    .event-cover img{width:100%;height:100%;object-fit:cover}
    .event-body{padding:12px;flex:1}
    .event-title{font-weight:700;color:#0f172a;margin:0 0 8px;font-size:1.08rem;line-height:1.25}
    .event-meta{font-size:.86rem;color:#475569;margin-bottom:4px}
    .event-desc{font-size:.85rem;color:#334155;margin-top:6px;max-height:3.2em;overflow:hidden}
    .event-actions{display:flex;gap:8px;margin-top:10px}
    .event-actions .btn{padding:6px 10px;border-radius:0.25rem}
    .ev-arrow{background:#fff;border:1px solid #e2e8f0;border-radius:0.25rem;width:34px;height:34px;display:flex;align-items:center;justify-content:center}
    @media (max-width:768px){.event-card{min-width:320px;max-width:360px}.event-cover{height:140px;width:45%;min-width:45%}}
    /* Compact mode for spotlight area */
    .events-strip.compact{margin:10px auto;border-radius:0.25rem}
    .events-strip.compact .head{padding:8px 12px}
    .events-strip.compact .event-card{min-width:420px;max-width:460px}
    .events-strip.compact .event-cover{height:150px}
    .events-strip.compact .event-title{font-size:1rem}
</style>

<div class="container">
    <div class="events-strip {{ !empty($compact) ? 'compact' : '' }}">
        <div id="eventsTrack" class="events-track">
            @foreach($events as $ev)
            @php
                $cover = $ev->banner_image ? asset($ev->banner_image) : asset('assets/images/cover.png');
                $start = $ev->startdate ? \Carbon\Carbon::parse($ev->startdate)->format('M d, Y') : '';
                $end   = $ev->enddate ? \Carbon\Carbon::parse($ev->enddate)->format('M d, Y') : '';
                $range = trim($start . ($end? ' - ' . $end : ''));
                $isPast = $ev->enddate
                    ? \Carbon\Carbon::parse($ev->enddate)->isPast()
                    : ($ev->startdate ? \Carbon\Carbon::parse($ev->startdate)->isPast() : false);
                $detailsUrl = url('events/'.$ev->id);
            @endphp
            <div class="event-card event-slide" style="cursor:pointer;" @if($detailsUrl) onclick="window.open('{{ $detailsUrl }}','_blank')" @endif>
                <div class="event-cover"><img src="{{ $cover }}" alt="{{ $ev->title }}" onerror="this.onerror=null;this.src='{{ asset('assets/images/cover.png') }}'"/></div>
                <div class="event-body">
                    <div class="event-title">{{ Str::limit(strip_tags($ev->title),70) }}</div>
                    <div class="event-meta"><i class="fa fa-clock-o mr-1"></i>{{ $range ?: 'Date TBA' }}</div>
                    @if($ev->venue)
                    <div class="event-meta"><i class="fa fa-map-marker mr-1"></i>{{ $ev->venue }}</div>
                    @endif
                    @if(!empty($ev->organized_by))
                    <div class="event-meta"><i class="fa fa-building mr-1"></i>{{ $ev->organized_by }}</div>
                    @endif
                    @if(!empty($ev->description))
                    <div class="event-desc">{{ Str::limit(strip_tags($ev->description), 140) }}</div>
                    @endif
                    <div class="event-actions">
                        @if(!$isPast && $ev->registration_link)
                        <a href="{{ $ev->registration_link }}" target="_blank" class="btn btn-sm btn-success" onclick="event.stopPropagation();"><i class="fa fa-edit mr-1"></i>Register</a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    // Smooth right-to-left auto scroll
    (function(){
        var track = document.getElementById('eventsTrack');
        if(!track) return;
        var step = 1;
        var gap = 12; // matches CSS gap
        function cardWidth(){
            var card = track.querySelector('.event-card');
            if(!card) return 300;
            var style = window.getComputedStyle(card);
            return card.getBoundingClientRect().width + gap;
        }
        // Start at the far right
        function toEnd(){ track.scrollLeft = track.scrollWidth; }
        toEnd();
        window.evSlide = function(dir){
            var delta = cardWidth();
            // dir: 1 means move right->left, -1 left->right
            track.scrollLeft -= (dir * delta);
            if(track.scrollLeft <= 0){ toEnd(); }
            if(track.scrollLeft >= track.scrollWidth - track.clientWidth){ track.scrollLeft = 0; }
        };
        setInterval(function(){ evSlide(1); }, 5000);
    })();
</script>
@endif


