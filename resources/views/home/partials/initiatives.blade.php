<style>
    .initiatives-section {
        background-image: 
            radial-gradient(circle at 25% 25%, rgba(17, 154, 72, 0.03) 0%, transparent 50%),
            radial-gradient(circle at 75% 75%, rgba(17, 154, 72, 0.02) 0%, transparent 50%);
        padding: 4rem 0;
        padding-top: 0px;
        position: relative;
        overflow: hidden;
        padding-top: 30px;
    }

    .initiatives-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-repeat: repeat;
        opacity: 0.03;
        pointer-events: none;
    }

    .section-header {
        text-align: center;
        margin-bottom: 3rem;
        position: relative;
        z-index: 2;
    }

    .section-title {
        font-size: 2.25rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 0.5rem;
        position: relative;
        display: inline-block;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 50%;
        transform: translateX(-50%);
        width: 50px;
        height: 3px;
        background: var(--theme-color-primary);
        border-radius: 0.25rem;
    }

    .section-subtitle {
        color: #64748b;
        font-size: 1.1rem;
        font-weight: 400;
        margin-top: 1rem;
    }

    /* Carousel Container */
    .carousel-container {
        position: relative;
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1rem;
        overflow: hidden;
    }


    .carousel-track {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        gap: 12px;
        padding: 12px;
        scroll-behavior: smooth;
    }

    .carousel-track::-webkit-scrollbar {
        height: 8px;
    }

    .carousel-track::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 0.25rem;
    }

    .carousel-slide {
        flex: 0 0 auto;
        min-width: 520px;
        max-width: 560px;
        scroll-snap-align: start;
    }

    .initiative-card {
        background: #ffffff;
        border-radius: 0.25rem;
        border: 1px solid #e2e8f0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        position: relative;
        overflow: hidden;
        display: flex;
        height: 170px;
    }

    .initiative-card:hover {
        box-shadow: 0 4px 12px rgba(17, 154, 72, 0.15), 0 2px 6px rgba(0, 0, 0, 0.1);
        border-color: var(--theme-color-primary);
    }

    .card-image {
        width: 42%;
        min-width: 42%;
        height: 170px;
        background: #f8fafc;
        border-right: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }

    .card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .initiative-card:hover .card-image img {
        transform: scale(1.05);
    }

    .card-content {
        padding: 12px;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .card-title {
        font-size: 1.08rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 8px;
        line-height: 1.25;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .card-author {
        color: #475569;
        font-size: 0.86rem;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .card-desc {
        font-size: 0.85rem;
        color: #334155;
        margin-top: 6px;
        max-height: 3.2em;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        flex: 1;
    }

    .card-meta {
        margin-top: auto;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        color: #64748b;
        font-size: 0.75rem;
        margin-bottom: 0.2rem;
    }

    .meta-item i {
        color: #119A48;
        width: 10px;
        text-align: center;
        font-size: 0.7rem;
    }

    .card-stats {
        display: flex;
        gap: 1rem;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid #e2e8f0;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        color: #64748b;
        font-size: 0.7rem;
    }

    .stat-item i {
        color: #119A48;
        font-size: 0.65rem;
    }


    /* Responsive Design */
    @media (max-width: 768px) {
        .initiatives-section {
            padding: 2.5rem 0;
        }

        .section-title {
            font-size: 1.875rem;
        }

        .carousel-slide {
            min-width: 320px;
            max-width: 360px;
        }

        .card-image {
            height: 140px;
            width: 45%;
            min-width: 45%;
        }

        .initiative-card {
            height: 140px;
        }

        .card-title {
            font-size: 1rem;
        }
    }

    @media (max-width: 480px) {
        .carousel-slide {
            min-width: 280px;
            max-width: 300px;
        }
    }
</style>

<section class="initiatives-section" id="initiatives">
    <div class="container">
        <div class="section-header">
            <h3 class="section-title">Strategic Initiatives</h3>
            <p class="section-subtitle">Curated resources from informed decision-making strategies and planning.</p>
        </div>

        <div class="carousel-container">
            <div class="carousel-track" id="carouselTrack">
                    @foreach ($initiatives as $index => $row)
                        @php
                            $imageUrl = $row->image_url ?? asset('assets/images/cover.png');
                            $authorName = @$row->author->name ?: 'Unknown Author';
                            $description = !empty($row->description) ? Str::limit(strip_tags($row->description), 140) : '';
                            $detailsUrl = url('records/resource') . '?id=' . $row->id;
                        @endphp
                        <div class="carousel-slide" style="cursor:pointer;" onclick="window.open('{{ $detailsUrl }}','_blank')">
                            <div class="initiative-card">
                                <div class="card-image">
                                    <img src="{{ $imageUrl }}" 
                                         alt="{{ $row->title }}" 
                                         loading="lazy"
                                         onerror="this.onerror=null;this.src='{{ asset('assets/images/cover.png') }}'" />
                                </div>
                                <div class="card-content">
                                    <div class="card-title">{{ Str::limit(strip_tags(clean_unicode($row->title)), 70) }}</div>
                                    <div class="card-author">
                                        <i class="fa fa-user mr-1"></i>
                                        <span>{{ clean_unicode($authorName) }}</span>
                                    </div>
                                    @if($description)
                                    <div class="card-desc">{{ clean_unicode($description) }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const track = document.getElementById('carouselTrack');
    if (!track) return;
    
    // Smooth right-to-left auto scroll (similar to events slider)
    var gap = 12; // matches CSS gap
    
    function cardWidth() {
        var card = track.querySelector('.carousel-slide');
        if (!card) return 300;
        return card.getBoundingClientRect().width + gap;
    }
    
    // Start at the far right
    function toEnd() { 
        track.scrollLeft = track.scrollWidth; 
    }
    toEnd();
    
    window.initSlide = function(dir) {
        var delta = cardWidth();
        // dir: 1 means move right->left, -1 left->right
        track.scrollLeft -= (dir * delta);
        if (track.scrollLeft <= 0) { 
            toEnd(); 
        }
        if (track.scrollLeft >= track.scrollWidth - track.clientWidth) { 
            track.scrollLeft = 0; 
        }
    };
    
    setInterval(function() { 
        initSlide(1); 
    }, 4000); // Move every 4 seconds
});
</script>
    let currentIndex = 0;
    let isPlaying = true;
    let autoPlayInterval;
    
    // Calculate slide width based on percentage and gap
    function getSlideWidth() {
        const containerWidth = track.parentElement.offsetWidth;
        const slidePercentage = 0.25; // 25% width
        const gap = 24; // 1.5rem = 24px gap
        return (containerWidth * slidePercentage) + gap;
    }
    
    // Calculate how many slides are visible
    function getVisibleSlides() {
        return 4; // Since each slide is 25%, 4 slides are visible
    }
    
    // Calculate maximum index
    function getMaxIndex() {
        const visibleSlides = getVisibleSlides();
        return Math.max(0, slides.length - visibleSlides);
    }
    
    // Update carousel position
    function updateCarousel() {
        const slideWidth = getSlideWidth();
        const translateX = -currentIndex * slideWidth;
        track.style.transform = `translateX(${translateX}px)`;
    }
    
    // Next slide
    function nextSlide() {
        const maxIndex = getMaxIndex();
        if (currentIndex < maxIndex) {
            currentIndex++;
        } else {
            currentIndex = 0; // Loop back to start
        }
        updateCarousel();
    }
    
    // Previous slide
    function prevSlide() {
        const maxIndex = getMaxIndex();
        if (currentIndex > 0) {
            currentIndex--;
        } else {
            currentIndex = maxIndex; // Loop to end
        }
        updateCarousel();
    }
    
    // Auto-play functionality
    function startAutoPlay() {
        autoPlayInterval = setInterval(nextSlide, 4000); // Change slide every 4 seconds
        isPlaying = true;
    }
    
    function stopAutoPlay() {
        clearInterval(autoPlayInterval);
        isPlaying = false;
    }
    
    // Pause on hover
    track.addEventListener('mouseenter', () => {
        if (isPlaying) {
            clearInterval(autoPlayInterval);
        }
    });
    
    track.addEventListener('mouseleave', () => {
        if (isPlaying) {
            startAutoPlay();
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', () => {
        updateCarousel();
    });
    
    // Initialize
    updateCarousel();
    startAutoPlay();
    
    // Optional: Add touch/swipe support for mobile
    let startX = 0;
    let isDragging = false;
    
    track.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
        isDragging = true;
        stopAutoPlay();
    });
    
    track.addEventListener('touchmove', (e) => {
        if (!isDragging) return;
        e.preventDefault();
    });
    
    track.addEventListener('touchend', (e) => {
        if (!isDragging) return;
        isDragging = false;
        
        const endX = e.changedTouches[0].clientX;
        const diffX = startX - endX;
        
        if (Math.abs(diffX) > 50) { // Minimum swipe distance
            if (diffX > 0) {
                nextSlide(); // Swipe left - next slide
            } else {
                prevSlide(); // Swipe right - previous slide
            }
        }
        
        startAutoPlay();
    });
});
</script>