@if(isset($events) && count($events))
<style>
    .events-strip{margin:16px auto 8px;border:1px solid #e2e8f0;border-radius:0.25rem;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,.06);overflow:hidden;position:relative}
    .events-strip .head{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-bottom:1px solid #eef2f7}
    .events-strip .head h5{margin:0;font-weight:700;color:#0f172a}
    .events-track{display:flex;overflow-x:auto;scroll-snap-type:x mandatory;gap:12px;padding:12px;scroll-behavior:smooth}
    .events-track::-webkit-scrollbar{height:8px}
    .events-track::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:0.25rem}
    .event-card{min-width:520px;max-width:560px;flex:0 0 auto;border:1px solid #e2e8f0;border-radius:0.25rem;scroll-snap-align:start;background:#fff;display:flex;overflow:hidden}
    .event-cover{width:42%;min-width:42%;height:255px;background:transparent;border-right:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0}
    .event-cover img{width:100%;height:100%;object-fit:contain;background:transparent}
    .events-nav{position:absolute;top:50%;transform:translateY(-50%);z-index:10;background:#fff;border:2px solid var(--theme-color-primary, #119A48);border-radius:50%;width:44px;height:44px;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.3s ease;box-shadow:0 2px 8px rgba(0,0,0,0.1)}
    .events-nav:hover{background:var(--theme-color-primary, #119A48);color:#fff;transform:translateY(-50%) scale(1.1);box-shadow:0 4px 12px rgba(0,0,0,0.2)}
    .events-nav.prev{left:10px}
    .events-nav.next{right:10px}
    .events-nav i{font-size:18px;color:var(--theme-color-primary, #119A48);transition:color 0.3s}
    .events-nav:hover i{color:#fff}
    @media (max-width:768px){.events-nav{width:36px;height:36px}.events-nav i{font-size:14px}.events-nav.prev{left:5px}.events-nav.next{right:5px}}
    .event-body{padding:12px;flex:1;height:255px;display:flex;flex-direction:column;overflow-y:auto;box-sizing:border-box}
    .event-title{font-weight:700;color:#0f172a;margin:0 0 8px;font-size:0.875rem;line-height:1.4;flex-shrink:0}
    .event-meta{font-size:.86rem;color:#475569;margin-bottom:4px;flex-shrink:0}
    .event-desc{font-size:.85rem;color:#334155;margin-top:6px;line-height:1.4;word-wrap:break-word;overflow-wrap:break-word;flex:1;min-height:0;overflow-y:auto}
    .event-desc *{max-width:100%}
    .event-actions{display:flex;gap:8px;margin-top:10px}
    .event-actions .btn{padding:6px 10px;border-radius:0.25rem}
    .ev-arrow{background:#fff;border:1px solid #e2e8f0;border-radius:0.25rem;width:34px;height:34px;display:flex;align-items:center;justify-content:center}
    @media (max-width:768px){
        .event-card{min-width:320px;max-width:360px;display:block !important;flex-direction:unset !important}
        .event-cover{height:120px !important;width:120px !important;min-width:120px !important;float:left !important;margin-right:12px !important;margin-bottom:8px !important;margin-left:0 !important;margin-top:0 !important;border-right:none !important;border-bottom:none;shape-outside:margin-box !important}
        .event-body{display:block !important;overflow:visible !important;text-align:justify !important;width:auto !important;padding:8px !important}
        .event-title{font-size:0.875rem !important;text-align:justify !important;overflow-wrap:break-word !important;white-space:normal !important;line-height:1.4 !important}
        .event-meta,.event-desc{text-overflow:unset !important;white-space:normal !important;overflow:visible !important;text-align:justify !important}
        .event-card::after{content:"";display:table;clear:both}
    }
    /* Compact mode for spotlight area */
    .events-strip.compact{margin:10px auto;border-radius:0.25rem}
    .events-strip.compact .head{padding:8px 12px}
    .events-strip.compact .event-card{min-width:420px;max-width:460px}
    .events-strip.compact .event-cover{height:225px}
    .events-strip.compact .event-title{font-size:0.875rem}
</style>

<div class="container">
    <div class="events-strip {{ !empty($compact) ? 'compact' : '' }}">
        <button class="events-nav prev" onclick="evSlide(-1)" aria-label="Previous events">
            <i class="fa fa-chevron-left"></i>
        </button>
        <button class="events-nav next" onclick="evSlide(1)" aria-label="Next events">
            <i class="fa fa-chevron-right"></i>
        </button>
        <div id="eventsTrack" class="events-track">
            @include('home.partials.theme1.event_list_items', ['events' => $events])
        </div>
    </div>
</div>

@if(!empty($homeEventsInfiniteScroll) && isset($eventsTotal) && $eventsTotal > count($events))
<div class="container mb-3" id="home-events-infinite-footer">
    <div id="home-events-infinite-sentinel" class="home-events-infinite-sentinel" aria-hidden="true" style="height:1px;"></div>
    <p class="text-muted small text-center mb-2" id="home-events-infinite-status">
        Showing {{ number_format(count($events)) }} of {{ number_format($eventsTotal) }} events
    </p>
    <div id="home-events-infinite-loader" class="text-muted small text-center d-none" aria-live="polite">
        <i class="fa fa-spinner fa-spin me-1"></i>Loading more events…
    </div>
</div>
@elseif(!empty($homeEventsInfiniteScroll) && isset($eventsTotal) && $eventsTotal > 0 && count($events) >= $eventsTotal)
<div class="container mb-3">
    <p class="text-muted small text-center mb-0" id="home-events-infinite-complete">All events loaded</p>
</div>
@endif

<script>
    // Smooth auto scroll with manual controls - continuous loop
    (function(){
        var track = document.getElementById('eventsTrack');
        if(!track) return;
        var gap = 12; // matches CSS gap
        var autoPlayInterval;
        var isPlaying = true;
        
        function cardWidth(){
            var card = track.querySelector('.event-card');
            if(!card) return 300;
            return card.getBoundingClientRect().width + gap;
        }
        
        function toEnd(){ 
            track.scrollLeft = track.scrollWidth - track.clientWidth; 
        }
        function toStart(){ 
            track.scrollLeft = 0; 
        }
        
        window.evSlide = function(dir){
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


