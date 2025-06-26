@php $primary = settings()->primary_color ?? '#222'; @endphp

<style>
    .category-card {
        background-color: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 0.75rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease-in-out;
    }

    .category-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        border-color: {{ $primary }};
        transform: translateY(-4px);
    }

    .category-icon {
        margin-bottom: 1rem;
    }

    .category-icon i {
        font-size: 2rem;
        color: var(--theme-color-primary, {{ $primary }});
    }

    .category-card h6 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #212529;
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

<section class="py-5 bg-light" id="categorization">
    <div class="container">
        <!-- Section Title -->
        <div class="row mb-4">
            <div class="col text-center">
                <h3 class="fw-bold text-primary">Explore Key Sections</h3>
                <p class="text-muted">Navigate through important content areas like health topics, contributors, and public forums.</p>
            </div>
        </div>

        <!-- Cards -->
        <div class="row g-4">
            @foreach($categories as $category)
                <div class="col-12 col-sm-6 col-md-3 d-flex">
                    <a href="{{ url($category['link']) }}" class="text-decoration-none w-100">
                        <div class="card category-card text-center p-4 d-flex flex-column justify-content-between h-100">
                            <div class="category-icon">
                                <i class="{{ $category['icon'] }}"></i>
                            </div>
                            <div>
                                <h6>{{ $category['title'] }}</h6>
                                @if(isset($category['description']))
                                    <p class="mt-2">{{ Str::limit($category['description'], 70) }}</p>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

