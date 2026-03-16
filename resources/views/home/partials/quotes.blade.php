@if(isset($quotes) && count($quotes) > 0)
<div class="quotes-slider-wrapper" id="quotes">
    <div class="quotes-slider px-2 py-2" id="quotesSlider">
        @foreach($quotes as $index => $quote)
            @php $quoteItem = is_object($quote) ? $quote : (object) $quote; @endphp
            <div class="quotes-slide {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
                <div class="reviews_wrap position-relative py-2 px-3 text-center">
                    @if(!empty($quoteItem->image_url ?? $quoteItem->image ?? null))
                        @php $imgSrc = $quoteItem->image_url ?? (strpos($quoteItem->image ?? '', 'http') === 0 ? $quoteItem->image : asset('storage/uploads/quotes/' . $quoteItem->image)); @endphp
                        <div class="quotes-slide-img mb-2">
                            @if(!empty($quoteItem->link_url))
                                <a href="{{ $quoteItem->link_url }}" target="_blank" rel="noopener" class="d-inline-block">
                                    <img src="{{ $imgSrc }}" alt="" class="rounded" style="max-height: 56px; width: auto; object-fit: contain;">
                                </a>
                            @else
                                <img src="{{ $imgSrc }}" alt="" class="rounded" style="max-height: 56px; width: auto; object-fit: contain;">
                            @endif
                        </div>
                    @endif
                    <p class="quotes-slide-text mb-0" style="color: var(--banner-text-color, #334155) !important; font-size: 1.05rem; line-height: 1.5;">{{ nl2br(e(Str::words($quoteItem->quote, 30))) }}</p>
                    @if(!empty($quoteItem->link_url))
                        <a href="{{ $quoteItem->link_url }}" target="_blank" rel="noopener" class="quotes-slide-link small mt-1 d-inline-block" style="color: var(--theme-color-primary, #119A48) !important;">Read more</a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    @if(count($quotes) > 1)
    <div class="quotes-slider-dots mt-2 d-flex justify-content-center gap-2 flex-wrap">
        @foreach($quotes as $index => $quote)
            <button type="button" class="quotes-dot btn btn-sm rounded-circle p-0 {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}" aria-label="Quote {{ $index + 1 }}"></button>
        @endforeach
    </div>
    @endif
</div>
<style>
/* Fixed min/max height for ~30 words to prevent layout flicker when slides change */
.quotes-slider-wrapper { position: relative; max-width: 720px; margin: 0 auto; min-height: 9rem; max-height: 9rem; border: none !important; background: transparent !important; }
.quotes-slider { overflow: hidden; min-height: 9rem; max-height: 9rem; position: relative; border: none !important; background: transparent !important; }
.quotes-slider .reviews_wrap { overflow: hidden; max-height: 8.5rem; border: none !important; background: transparent !important; box-shadow: none !important; }
.quotes-slider .quotes-slide-text { display: -webkit-box; -webkit-line-clamp: 5; -webkit-box-orient: vertical; overflow: hidden; }
.quotes-slide { position: absolute; left: 0; right: 0; top: 0; opacity: 0; transform: translateY(-24px); pointer-events: none; transition: opacity 0.5s ease, transform 0.5s ease; }
.quotes-slide.active { position: relative; opacity: 1; transform: translateY(0); pointer-events: auto; }
.quotes-slide.leave-up { transform: translateY(-100%); opacity: 0; }
.quotes-slide.enter-from-up { transform: translateY(-24px); opacity: 0; }
.quotes-slide.enter-from-up.active { transform: translateY(0); opacity: 1; }
.quotes-slider-dots .quotes-dot { width: 8px; height: 8px; background: rgba(0,0,0,0.2); border: none; transition: background 0.3s, transform 0.3s; }
.quotes-slider-dots .quotes-dot.active { background: var(--theme-color-primary, #119A48); transform: scale(1.2); }
.quotes-slider-dots .quotes-dot:hover { background: rgba(0,0,0,0.35); }
</style>
<script>
(function() {
    var wrapper = document.getElementById('quotes');
    if (!wrapper) return;
    var slider = document.getElementById('quotesSlider');
    var slides = wrapper.querySelectorAll('.quotes-slide');
    var dots = wrapper.querySelectorAll('.quotes-dot');
    if (slides.length <= 1) return;
    var current = 0;
    var interval = 6000;
    var timer = null;
    function goTo(index) {
        if (index === current) return;
        var from = current, to = index;
        var fromEl = slides[from], toEl = slides[to];
        fromEl.classList.remove('active');
        fromEl.classList.add('leave-up');
        toEl.style.position = 'absolute';
        toEl.style.top = '0';
        toEl.style.left = '0';
        toEl.style.right = '0';
        toEl.classList.add('enter-from-up');
        toEl.offsetHeight;
        toEl.classList.add('active');
        setTimeout(function() {
            fromEl.classList.remove('leave-up');
            fromEl.style.position = '';
            toEl.classList.remove('enter-from-up');
            toEl.style.position = 'relative';
        }, 500);
        current = to;
        dots.forEach(function(d, i) { d.classList.toggle('active', i === current); });
    }
    function next() { goTo((current + 1) % slides.length); }
    function startTimer() { timer = setInterval(next, interval); }
    function stopTimer() { if (timer) clearInterval(timer); }
    dots.forEach(function(dot) {
        dot.addEventListener('click', function() {
            var idx = parseInt(this.getAttribute('data-index'), 10);
            if (!isNaN(idx)) { stopTimer(); goTo(idx); startTimer(); }
        });
    });
    startTimer();
    wrapper.addEventListener('mouseenter', stopTimer);
    wrapper.addEventListener('mouseleave', startTimer);
})();
</script>
@endif