@if(isset($initiatives) && count($initiatives))
<style>
    .initiatives-strip{margin:16px auto 8px;border:1px solid #e2e8f0;border-radius:0.25rem;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,.06);overflow:hidden}
    .initiatives-track{display:flex;overflow-x:auto;scroll-snap-type:x mandatory;gap:12px;padding:12px;scroll-behavior:smooth}
    .initiatives-track::-webkit-scrollbar{height:8px}
    .initiatives-track::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:0.25rem}
    .initiative-card{min-width:520px;max-width:560px;flex:0 0 auto;border:1px solid #e2e8f0;border-radius:0.25rem;scroll-snap-align:start;background:#fff;display:flex;overflow:hidden}
    .initiative-cover{width:42%;min-width:42%;height:170px;background:#f8fafc;border-right:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;overflow:hidden}
    .initiative-cover img{width:100%;height:100%;object-fit:cover}
    .initiative-body{padding:12px;flex:1}
    .initiative-title{font-weight:700;color:#0f172a;margin:0 0 8px;font-size:1.08rem;line-height:1.25}
    .initiative-meta{font-size:.86rem;color:#475569;margin-bottom:4px}
    .initiative-desc{font-size:.85rem;color:#334155;margin-top:6px;max-height:3.2em;overflow:hidden}
    @media (max-width:768px){.initiative-card{min-width:320px;max-width:360px}.initiative-cover{height:140px;width:45%;min-width:45%}}
</style>

<div class="container">
    <div class="row justify-content-center" data-aos="fade-in">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="sec_title position-relative text-center mb-3" style="margin-bottom: 1rem !important;">
                <h2 class="ft-bold">Flagship Initiatives</h2>
            </div>
        </div>
    </div>
    <div class="initiatives-strip">
        <div id="initiativesTrack" class="initiatives-track">
            @foreach($initiatives as $row)
            @php
                $imageUrl = $row->image_url ?? asset('assets/images/cover.png');
                $authorName = @$row->author->name ?: 'Unknown Author';
                $description = !empty($row->description) ? Str::limit(strip_tags($row->description), 140) : '';
                $detailsUrl = url('records/resource') . '?id=' . $row->id;
            @endphp
            <div class="initiative-card initiative-slide" style="cursor:pointer;" onclick="window.open('{{ $detailsUrl }}','_blank')">
                <div class="initiative-cover"><img src="{{ $imageUrl }}" alt="{{ $row->title }}" onerror="this.onerror=null;this.src='{{ asset('assets/images/cover.png') }}'"/></div>
                <div class="initiative-body">
                    <div class="initiative-title">{{ Str::limit(strip_tags(clean_unicode($row->title)),70) }}</div>
                    <div class="initiative-meta"><i class="fa fa-user mr-1"></i>{{ clean_unicode($authorName) }}</div>
                    @if($description)
                    <div class="initiative-desc">{{ clean_unicode($description) }}</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    // Smooth right-to-left auto scroll (similar to events slider)
    (function(){
        var track = document.getElementById('initiativesTrack');
        if(!track) return;
        var gap = 12; // matches CSS gap
        function cardWidth(){
            var card = track.querySelector('.initiative-card');
            if(!card) return 300;
            return card.getBoundingClientRect().width + gap;
        }
        // Start at the far right
        function toEnd(){ track.scrollLeft = track.scrollWidth; }
        toEnd();
        window.initSlide = function(dir){
            var delta = cardWidth();
            // dir: 1 means move right->left, -1 left->right
            track.scrollLeft -= (dir * delta);
            if(track.scrollLeft <= 0){ toEnd(); }
            if(track.scrollLeft >= track.scrollWidth - track.clientWidth){ track.scrollLeft = 0; }
        };
        setInterval(function(){ initSlide(1); }, 4000);
    })();
</script>
@endif