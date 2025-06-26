

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
