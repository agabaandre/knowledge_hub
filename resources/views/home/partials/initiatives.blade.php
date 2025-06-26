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
        border-radius: 2px;
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

    .carousel-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
    }

    .carousel-track {
        display: flex;
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        gap: 1.5rem;
    }

    .carousel-slide {
        flex: 0 0 auto;
        width: 25%;
    }

    .initiative-card {
        background: #ffffff;
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(0, 0, 0, 0.04);
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem;
    }

    .initiative-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--theme-color-primary);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .initiative-card:hover {
        box-shadow: 0 8px 25px rgba(17, 154, 72, 0.1), 0 4px 10px rgba(0, 0, 0, 0.1);
        border-color: var(--theme-color-primary);
    }

    .initiative-card:hover::before {
        transform: scaleX(1);
    }

    .card-header {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        align-items: flex-start;
    }

    .card-image {
        flex-shrink: 0;
        width: 70px;
        height: 70px;
        border-radius: 8px;
        overflow: hidden;
        background: #f1f5f9;
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
        flex: 1;
        min-width: 0;
    }

    .card-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 0.25rem;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .card-author {
        color: #64748b;
        font-size: 0.8rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
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

    .initiative-link {
        text-decoration: none;
        color: inherit;
        display: block;
        height: 100%;
    }

    .initiative-link:hover {
        text-decoration: none;
        color: inherit;
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
            width: 50%;
        }

        .initiative-card {
            padding: 1.25rem;
        }

        .card-image {
            width: 60px;
            height: 60px;
        }

        .card-title {
            font-size: 0.9rem;
        }
    }

    @media (max-width: 480px) {
        .carousel-slide {
            width: 100%;
        }

        .card-header {
            gap: 0.75rem;
        }

        .card-stats {
            gap: 0.75rem;
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
            <div class="carousel-wrapper">
                <div class="carousel-track" id="carouselTrack">
                    @foreach ($initiatives as $index => $row)
                        <div class="carousel-slide">
                            <a href="{{ url('records/resource') }}?id={{ $row->id }}" class="initiative-link">
                                <div class="initiative-card">
                                        <div class="card-image">
                                            <img src="{{ $row->image_url }}" 
                                                 alt="Cover image of {{ $row->title }}" 
                                                 loading="lazy" />
                                        </div>
                                        <div class="card-content">
                                            <h4 class="card-title">{{ truncate($row->title, 45) }}</h4>
                                            <div class="card-author">
                                                <i class="fa fa-user"></i>
                                                <span>{{ truncate(@$row->author->name ?: 'Unknown Author', 20) }}</span>
                                            </div>
                                        </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const track = document.getElementById('carouselTrack');
    const slides = track.querySelectorAll('.carousel-slide');
    
    if (!track || slides.length === 0) return;
    
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