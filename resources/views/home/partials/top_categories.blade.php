
@php $primary = settings()->primary_color ?? '#222'; @endphp

<style>
    .category-card {
        background-color: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 0.75rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease-in-out;
        height: 100%;
    }

    .category-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        transform: translateY(-3px);
        border-color: {{ $primary }};
    }

    .category-icon i {
        color: var(--theme-color-primary, #0d6efd);
    }

    .category-icon {
        margin-bottom: 1rem;
    }

    .category-card h6 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .category-card p {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0;
    }

    @media (max-width: 575.98px) {
        .category-card {
            padding: 1.2rem;
        }
    }
</style>

<!-- ================================ Categorization Section ================================ -->
<section class="py-5 bg-light" id="categorization">
    <div class="container">
        <!-- Section Header -->
        <div class="row mb-4">
            <div class="col text-center">
                <h3 class="fw-bold text-primary">Explore Key Sections</h3>
                <p class="text-muted mb-0">Browse health topics, contributors, coverage, and forums to navigate the platform efficiently.</p>
            </div>
        </div>

        <!-- Category Cards -->
        <div class="row g-4">
            @foreach($categories as $category)
                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{ url($category['link']) }}" class="text-decoration-none">
                        <div class="card category-card text-center p-4">
                            <div class="category-icon text-primary">
                                <i class="{{ $category['icon'] }} fa-2x"></i>
                            </div>
                            <h6 class="text-dark">{{ $category['title'] }}</h6>
                            @if(isset($category['description']))
                                <p class="mt-2">{{ Str::limit($category['description'], 80) }}</p>
                            @endif
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
