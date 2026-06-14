@if(isset($initiatives) && count($initiatives))
<style>
    .initiatives-strip{margin:16px auto 8px;border:1px solid #e2e8f0;border-radius:0.25rem;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,.06);overflow:hidden;position:relative}
    .initiatives-track{display:flex;overflow-x:auto;scroll-snap-type:x mandatory;gap:12px;padding:12px;scroll-behavior:smooth}
    .initiatives-track::-webkit-scrollbar{height:8px}
    .initiatives-track::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:0.25rem}
    .initiative-card{min-width:520px;max-width:560px;flex:0 0 auto;border:1px solid #e2e8f0;border-radius:0.25rem;scroll-snap-align:start;background:#fff;display:flex;overflow:hidden}
    .initiative-cover{width:42%;min-width:42%;height:275px;background:transparent;border-right:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0}
    .initiative-cover img{width:100%;height:100%;object-fit:contain;background:transparent}
    .initiative-body{padding:12px;flex:1;height:275px;display:flex;flex-direction:column;overflow-y:auto;box-sizing:border-box}
    .initiative-title{font-weight:700;color:#0f172a;margin:0 0 8px;font-size:0.875rem;line-height:1.4;flex-shrink:0}
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
    .initiatives-strip .sec_title { padding: 1rem 12px 0; }
    .initiative-share-dropdown{position:fixed;z-index:1060;background:#fff;border:1px solid #e2e8f0;border-radius:0.25rem;box-shadow:0 4px 12px rgba(0,0,0,0.15);padding:6px 0;min-width:160px}
    .initiative-share-dropdown a,.initiative-share-dropdown button{display:flex;align-items:center;gap:8px;width:100%;padding:8px 12px;border:none;background:0;color:#334155;text-decoration:none;font-size:0.875rem;cursor:pointer;text-align:left}
    .initiative-share-dropdown a:hover,.initiative-share-dropdown button:hover{background:#f1f5f9}
    .initiative-share-dropdown i{width:20px;text-align:center}
    .initiative-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:6px;flex-shrink:0}
    .initiative-actions .btn-share-init{border:1px solid #e2e8f0;background:#fff;color:#64748b;padding:0.25rem 0.5rem;font-size:0.8rem;border-radius:0.25rem;cursor:pointer}
    .initiative-actions .btn-share-init:hover{background:#f8fafc;color:var(--theme-color-primary,#119A48)}
    @media (max-width:768px){
        .initiative-card{min-width:320px;max-width:360px;display:block !important;flex-direction:unset !important}
        .initiative-cover{height:120px !important;width:120px !important;min-width:120px !important;float:left !important;margin-right:12px !important;margin-bottom:8px !important;margin-left:0 !important;margin-top:0 !important;border-right:none !important;border-bottom:none;shape-outside:margin-box !important}
        .initiative-body{display:block !important;overflow:visible !important;text-align:justify !important;width:auto !important;height:auto !important;padding:8px !important;flex-direction:unset !important}
        .initiative-title{font-size:0.875rem !important;text-align:justify !important;overflow-wrap:break-word !important;white-space:normal !important;line-height:1.4 !important}
        .initiative-meta,.initiative-desc{text-overflow:unset !important;white-space:normal !important;overflow:visible !important;text-align:justify !important}
        .initiative-card::after{content:"";display:table;clear:both}
        .initiatives-nav{width:36px;height:36px}.initiatives-nav i{font-size:14px}.initiatives-nav.prev{left:5px}.initiatives-nav.next{right:5px}
    }
</style>

    <div class="container">
    <div class="initiatives-strip">
        <div class="sec_title position-relative text-center py-3 mb-0" style="margin-bottom: 0 !important;">
            <h2 class="ft-bold mb-0 notranslate" style="color: #1e293b;" data-khub-i18n="home_sections.flagship_initiatives">{{ \App\Support\UiLocaleLabels::homeSection('flagship_initiatives') }}</h2>
        </div>
        <button class="initiatives-nav prev" onclick="initSlide(-1)" aria-label="Previous initiatives">
            <i class="fa fa-chevron-left"></i>
        </button>
        <button class="initiatives-nav next" onclick="initSlide(1)" aria-label="Next initiatives">
            <i class="fa fa-chevron-right"></i>
        </button>
        <div id="initiativesTrack" class="initiatives-track">
            @foreach($initiatives as $row)
            @php
                $imageUrl = resolve_publication_card_cover($row);
                $authorName = @$row->author->name ?: 'Unknown Author';
                // Description is HTML content, will be displayed directly with CSS truncation
                $description = !empty($row->description) ? $row->description : '';
                $detailsUrl = publication_url($row);
                $publicationUrl = $row->publication ?? null;
                $theme = $row->theme->description ?? '';
                $subTheme = $row->sub_theme->description ?? '';
                $visits = $row->visits ?? 0;
                $commentsCount = count($row->comments ?? []);
            @endphp
            <article class="initiative-card initiative-slide">
                <div class="initiative-cover"><img src="{{ $imageUrl }}" alt="{{ $row->title }}" onerror="this.onerror=null;this.src='{{ asset('assets/images/cover.png') }}'"/></div>
                <div class="initiative-body">
                    <h3 class="initiative-title" style="overflow-wrap: break-word; word-wrap: break-word;">
                        <a href="{{ $detailsUrl }}" class="text-decoration-none text-reset">{{ Str::limit(strip_tags(clean_unicode($row->title)),70) }}</a>
                    </h3>
                    <div class="initiative-meta">
                        <span class="notranslate" translate="no"><i class="fa fa-user"></i> {{ clean_unicode($authorName) }}</span>
                        @if($publicationUrl)
                        <span><i class="fa fa-link"></i> <a href="{{ $publicationUrl }}" target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation();" style="color: inherit; text-decoration: underline;" class="notranslate" data-khub-i18n="home_sections.source">{{ __('home_sections.source') }}</a></span>
                        @endif
                        @if($theme)
                        <span><i class="fa fa-briefcase"></i> {{ Str::limit($theme, 20) }}</span>
                        @endif
                    </div>
                    <div class="initiative-meta">
                        <span><i class="fa fa-eye"></i> {{ format_view_count($visits) }} Views</span>
                        <span><i class="fa fa-comments"></i> {{ $commentsCount }} Comments</span>
                    </div>
                    <div class="initiative-actions" onclick="event.stopPropagation();">
                        <button type="button" class="btn-share-init" title="Share" data-details-url="{{ $detailsUrl }}" data-title="{{ e(Str::limit(strip_tags(clean_unicode($row->title)), 70)) }}"><i class="fa fa-share-alt"></i> Share</button>
                    </div>
                    @if($description)
                    <div class="initiative-desc">{!! Str::words(strip_tags($description), 30, '...') !!}</div>
                    @endif
                </div>
            </article>
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

    (function initiativeShare() {
        document.addEventListener('click', function(e) {
            var btn = e.target.closest('.btn-share-init');
            if (!btn) return;
            e.preventDefault();
            e.stopPropagation();
            var url = btn.getAttribute('data-details-url') || window.location.href;
            var title = btn.getAttribute('data-title') || '';

            function tryNativeShare() {
                if (typeof navigator !== 'undefined' && navigator.share) {
                    return navigator.share({ title: title || 'Resource', url: url }).then(function() { return true; }).catch(function() { return false; });
                }
                return Promise.resolve(false);
            }
            function openDropdown() {
                var existing = document.getElementById('initiative-share-dropdown');
                if (existing) existing.remove();
                var drop = document.createElement('div');
                drop.id = 'initiative-share-dropdown';
                drop.className = 'initiative-share-dropdown';
                var twitterText = (title ? title + ' ' : '') + url;
                var twitterUrl = 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(twitterText);
                var linkedInUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(url);
                var facebookUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url);
                var mailUrl = 'mailto:?subject=' + encodeURIComponent(title || 'Resource') + '&body=' + encodeURIComponent(url);
                drop.innerHTML = '<a href="' + twitterUrl + '" target="_blank" rel="noopener"><i class="fab fa-twitter"></i> X (Twitter)</a><a href="' + linkedInUrl + '" target="_blank" rel="noopener"><i class="fab fa-linkedin"></i> LinkedIn</a><a href="' + facebookUrl + '" target="_blank" rel="noopener"><i class="fab fa-facebook"></i> Facebook</a><button type="button" class="initiative-share-copy"><i class="fa fa-copy"></i> Copy</button><a href="' + mailUrl + '"><i class="fa fa-envelope"></i> Email</a>';
                document.body.appendChild(drop);
                var rect = btn.getBoundingClientRect();
                drop.style.left = Math.max(8, Math.min(rect.left, window.innerWidth - 180)) + 'px';
                drop.style.top = (rect.bottom + 4) + 'px';
                drop.querySelector('.initiative-share-copy').addEventListener('click', function() {
                    try {
                        navigator.clipboard.writeText(url);
                        var copyBtn = this;
                        copyBtn.innerHTML = '<i class="fa fa-check"></i> Copied';
                        setTimeout(function() { copyBtn.innerHTML = '<i class="fa fa-copy"></i> Copy'; }, 1500);
                    } catch (err) { alert('Copy failed.'); }
                });
                function closeMenu() { drop.remove(); document.removeEventListener('click', closeMenu); }
                setTimeout(function() { document.addEventListener('click', closeMenu); }, 0);
            }
            tryNativeShare().then(function(used) { if (!used) openDropdown(); });
        });
    })();
</script>
@endif