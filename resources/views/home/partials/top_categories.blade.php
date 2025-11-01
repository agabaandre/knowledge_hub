@php $primary = settings()->primary_color ?? '#222'; @endphp



<style>
    .category-card {
        background-color: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 0.25rem;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        transition: all 0.15s ease-in-out;
        padding: 1.25rem;
        min-height: 180px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    .category-card:hover {
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
        border-color: {{ $primary }};
        transform: translateY(-2px);
    }

    .category-icon i {
        font-size: 1.8rem;
        color: var(--theme-color-primary, {{ $primary }});
        margin-bottom: 0.6rem;
    }

    .category-card h6 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.3rem;
        color: #212529;
    }

    .category-card p {
        font-size: 0.82rem;
        color: #6c757d;
        margin-bottom: 0;
    }

    @media (max-width: 575.98px) {
        .category-card {
            min-height: auto;
            padding: 1rem;
        }
    }
</style>

<section class="py-5 bg-light" id="categorization" style="margin-bottom: 0;">
    <div class="container">
        <!-- Section Title -->
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="sec_title text-center">
                    <h2>Explore Key Sections</h2>
                </div>
            </div>
        </div>

        <!-- Cards -->
        <div class="row g-3" style="margin-top: 2rem;">
            @foreach($categories as $category)
                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{ url($category['link']) }}" class="text-decoration-none">
                        <div class="category-card">
                            <div class="category-icon">
                                <i class="{{ $category['icon'] }}"></i>
                            </div>
                            <h6>{{ $category['title'] }}</h6>
                            @if(isset($category['description']))
                                <p>{{ Str::limit($category['description'], 60) }}</p>
                            @endif
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

