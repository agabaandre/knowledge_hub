@extends('layouts.app')

@section('title', $tag->tag_text . ' - Health Topics')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3>{{ $tag->tag_text }}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('health-topics.index') }}">Health Topics</a></li>
                    <li class="breadcrumb-item active">{{ $tag->tag_text }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="health-topic-icon me-3">
                            <i class="fa fa-exclamation-triangle text-danger"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">{{ $tag->tag_text }}</h5>
                            <p class="card-text text-muted mb-0">Health Emergency Topic</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($tag->overview)
                        <div class="topic-overview mb-4">
                            <h6 class="text-primary mb-2">Overview</h6>
                            <div class="overview-content">
                                {!! $tag->overview !!}
                            </div>
                        </div>
                    @endif

                    <div class="publications-section">
                        <h6 class="text-primary mb-3">Related Publications</h6>
                        
                        @if($publications->count() > 0)
                            <div class="row">
                                @foreach($publications as $publication)
                                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4">
                                        <div class="card h-100 publication-card">
                                            @if($publication->cover)
                                                <img src="{{ $publication->cover }}" class="card-img-top publication-image" alt="{{ $publication->title }}">
                                            @endif
                                            <div class="card-body">
                                                <h6 class="card-title">
                                                    <a href="{{ url('records/resource?id=' . $publication->id) }}" class="text-decoration-none">
                                                        {{ Str::limit($publication->title, 80) }}
                                                    </a>
                                                </h6>
                                                <p class="card-text text-muted small">
                                                    {{ Str::limit(strip_tags($publication->description), 120) }}
                                                </p>
                                                <div class="publication-meta">
                                                    @if($publication->author)
                                                        <small class="text-muted">
                                                            <i class="fa fa-user me-1"></i>
                                                            {{ $publication->author->name }}
                                                        </small>
                                                    @endif
                                                    @if($publication->created_at)
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fa fa-calendar me-1"></i>
                                                            {{ $publication->created_at->format('M Y') }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="card-footer bg-transparent">
                                                <a href="{{ url('records/resource?id=' . $publication->id) }}" class="btn btn-outline-primary btn-sm">
                                                    View Details <i class="fa fa-arrow-right ms-1"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            @if($publications->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $publications->links() }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fa fa-file-alt fa-3x text-muted mb-3"></i>
                                    <h5>No Publications Found</h5>
                                    <p class="text-muted">There are currently no publications tagged with "{{ $tag->tag_text }}".</p>
                                    <a href="{{ url('records') }}" class="btn btn-primary">
                                        Browse All Publications
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.health-topic-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.topic-overview {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 0.5rem;
    border-left: 4px solid #007bff;
}

.overview-content {
    line-height: 1.6;
}

.publication-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: 1px solid #e3e6f0;
}

.publication-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
}

.publication-image {
    height: 200px;
    object-fit: cover;
}

.publication-card .card-title a {
    color: #2c3e50;
    font-weight: 600;
}

.publication-card .card-title a:hover {
    color: #007bff;
}

.publication-meta {
    margin-top: 1rem;
}

.empty-state {
    padding: 2rem;
}

@media (max-width: 768px) {
    .publication-card {
        margin-bottom: 1rem;
    }
    
    .topic-overview {
        padding: 1rem;
    }
}
</style>
@endsection 