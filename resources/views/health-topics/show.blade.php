<div class="row g-4">
    @foreach($publications as $publication)
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden publication-card d-flex flex-column">
                
                <!-- Image Container with fixed height -->
                <div class="image-container">
                    <img src="{{ $publication->image_url ?? $publication->cover ?? asset('assets/img/default-publication.png') }}"
                         alt="{{ $publication->title }}"
                         class="publication-img">
                </div>

                <!-- Card Content -->
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <a href="{{ url('records/resource?id=' . $publication->id) }}" class="text-decoration-none pub-title-link text-dark">
                            <h6 class="fw-semibold mb-2 pub-title">
                                {{ Str::limit(strip_tags($publication->title), 90) }}
                            </h6>
                        </a>
                        <p class="pub-desc text-muted mb-3">
                            {{ Str::limit(strip_tags($publication->description), 120) }}
                        </p>
                    </div>
                    <div class="pub-meta small text-secondary">
                        @if($publication->author)
                            <div><i class="fa fa-user me-1"></i>{{ $publication->author->name }}</div>
                        @endif
                        @if($publication->created_at)
                            <div><i class="fa fa-calendar me-1"></i>{{ $publication->created_at->format('M Y') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
