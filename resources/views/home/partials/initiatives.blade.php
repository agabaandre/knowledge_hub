@if(isset($initiatives) && count($initiatives))
<style>
    .initiatives-strip{margin:16px auto 8px;border:1px solid #e2e8f0;border-radius:0.25rem;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,.06);overflow:hidden;position:relative}
    .initiatives-track{display:flex;overflow-x:auto;scroll-snap-type:x mandatory;gap:12px;padding:12px;scroll-behavior:smooth}
    .initiatives-track::-webkit-scrollbar{height:8px}
    .initiatives-track::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:0.25rem}
    .initiative-card{min-width:520px;max-width:560px;flex:0 0 auto;border:1px solid #e2e8f0;border-radius:0.25rem;scroll-snap-align:start;background:#fff;display:flex;overflow:hidden}
    .initiative-cover{width:42%;min-width:42%;height:255px;background:transparent;border-right:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;overflow:hidden}
    .initiative-cover img{width:100%;height:100%;object-fit:contain;background:transparent}
    .initiative-body{padding:12px;flex:1;height:255px;display:flex;flex-direction:column;overflow-y:auto;box-sizing:border-box}
    .initiative-title{font-weight:700;color:#0f172a;margin:0 0 8px;font-size:1.08rem;line-height:1.25;flex-shrink:0}
    .initiative-meta{font-size:.86rem;color:#475569;margin-bottom:4px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;flex-shrink:0}
    .initiative-meta i{color:var(--theme-color-primary, #119A48);font-size:0.75rem}
    .initiative-desc{font-size:.85rem;color:#334155;margin-top:6px;line-height:1.4;word-wrap:break-word;overflow-wrap:break-word;flex:1;min-height:0;overflow-y:auto}
    .initiative-desc *{max-width:100%}
    .initiatives-nav{position:absolute;top:50%;transform:translateY(-50%);z-index:10;background:#fff;border:2px solid var(--theme-color-primary, #119A48);border-radius:50%;width:44px;height:44px;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.3s ease;box-shadow:0 2px 8px rgba(0,0,0,0.1)}
    .initiatives-nav:hover{background:var(--theme-color-primary, #119A48);color:#fff;transform:translateY(-50%) scale(1.1);box-shadow:0 4px 12px rgba(0,0,0,0.2)}
    .initiatives-nav.prev{left:10px}
    .initiatives-nav.next{right:10px}
    .initiatives-nav i{font-size:18px;color:var(--theme-color-primary, #119A48);transition:color 0.3s}
    .initiatives-nav:hover i{color:#fff}
    @media (max-width:768px){.initiative-card{min-width:320px;max-width:360px}.initiative-cover{height:140px;width:45%;min-width:45%}.initiative-body{height:140px}.initiatives-nav{width:36px;height:36px}.initiatives-nav i{font-size:14px}.initiatives-nav.prev{left:5px}.initiatives-nav.next{right:5px}}
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
        <button class="initiatives-nav prev" onclick="initSlide(-1)" aria-label="Previous initiatives">
            <i class="fa fa-chevron-left"></i>
        </button>
        <button class="initiatives-nav next" onclick="initSlide(1)" aria-label="Next initiatives">
            <i class="fa fa-chevron-right"></i>
        </button>
        <div id="initiativesTrack" class="initiatives-track">
            @foreach($initiatives as $row)
            @php
                $imageUrl = $row->image_url ?? asset('assets/images/cover.png');
                $authorName = @$row->author->name ?: 'Unknown Author';
                // Description is HTML content, will be displayed directly with CSS truncation
                $description = !empty($row->description) ? $row->description : '';
                $detailsUrl = url('records/resource') . '?id=' . $row->id;
                $publicationUrl = $row->publication ?? null;
                $theme = $row->theme->description ?? '';
                $subTheme = $row->sub_theme->description ?? '';
                $visits = $row->visits ?? 0;
                $commentsCount = count($row->comments ?? []);
            @endphp
            <div class="initiative-card initiative-slide" style="cursor:pointer;" onclick="window.open('{{ $detailsUrl }}','_blank')">
                <div class="initiative-cover"><img src="{{ $imageUrl }}" alt="{{ $row->title }}" onerror="this.onerror=null;this.src='{{ asset('assets/images/cover.png') }}'"/></div>
                <div class="initiative-body">
                    <div class="initiative-title">{{ Str::limit(strip_tags(clean_unicode($row->title)),70) }}</div>
                    <div class="initiative-meta">
                        <span><i class="fa fa-user"></i> {{ clean_unicode($authorName) }}</span>
                        @if($publicationUrl)
                        <span><i class="fa fa-link"></i> <a href="{{ $publicationUrl }}" target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation();" style="color: inherit; text-decoration: underline;">Source</a></span>
                        @endif
                        @if($theme)
                        <span><i class="fa fa-briefcase"></i> {{ Str::limit($theme, 20) }}</span>
                        @endif
                        @if($visits > 0)
                        <span><i class="fa fa-eye"></i> {{ $visits }} Views</span>
                        @endif
                        @if($commentsCount > 0)
                        <span><i class="fa fa-comments"></i> {{ $commentsCount }} Comments</span>
                        @endif
                                            </div>
                    @if($description)
                    <div class="initiative-desc">{!! $description !!}</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    // Smooth auto scroll with manual controls - continuous loop
    (function(){
        var track = document.getElementById('initiativesTrack');
        if(!track) return;
        var gap = 12; // matches CSS gap
        var autoPlayInterval;
        var isPlaying = true;
        
        function cardWidth(){
            var card = track.querySelector('.initiative-card');
            if(!card) return 300;
            return card.getBoundingClientRect().width + gap;
        }
        
        function toEnd(){ 
            track.scrollLeft = track.scrollWidth - track.clientWidth; 
        }
        function toStart(){ 
            track.scrollLeft = 0; 
        }
        
        window.initSlide = function(dir){
            var delta = cardWidth();
            var newScrollLeft = track.scrollLeft - (dir * delta);
            
            // Check if we've reached the boundaries
            var maxScroll = track.scrollWidth - track.clientWidth;
            
            if(newScrollLeft <= 0){
                // We're at or past the start, jump to end for seamless loop
                toEnd();
            } else if(newScrollLeft >= maxScroll - 5){
                // We're at or past the end, jump to start for seamless loop
                toStart();
            } else {
                track.scrollLeft = newScrollLeft;
            }
            
            // Reset auto-play timer on manual navigation
            if(isPlaying){
                clearInterval(autoPlayInterval);
                startAutoPlay();
            }
        };
        
        function startAutoPlay(){
            isPlaying = true;
            clearInterval(autoPlayInterval); // Clear any existing interval
            
            autoPlayInterval = setInterval(function(){ 
                if(!isPlaying) return; // Don't play if paused
                
                var delta = cardWidth();
                var maxScroll = track.scrollWidth - track.clientWidth;
                var currentScroll = track.scrollLeft;
                
                // Check if we're at the end (with small threshold for rounding)
                if(currentScroll >= maxScroll - 5){
                    // We've reached the end, reset to start for continuous loop
                    toStart();
                } else {
                    // Continue scrolling forward (to the right)
                    var newScroll = currentScroll + delta;
                    
                    // Check if this move would take us past the end
                    if(newScroll >= maxScroll - 5){
                        // Move to end first
                        track.scrollLeft = maxScroll;
                        // Then reset to start after a brief pause for seamless loop
                        setTimeout(function(){
                            toStart();
                        }, 300);
                    } else {
                        // Normal forward scroll
                        track.scrollLeft = newScroll;
                    }
                }
            }, 5000);
        }
        
        // Initialize - start from the beginning
        toStart();
        startAutoPlay();
        
        // Pause on hover
        track.addEventListener('mouseenter', function(){
            isPlaying = false;
            clearInterval(autoPlayInterval);
        });
        track.addEventListener('mouseleave', function(){
            startAutoPlay();
        });
    })();
</script>
@endif