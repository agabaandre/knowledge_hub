@foreach ($tags as $tag)
    <a href="{{ health_topic_url($tag) }}" class="topic-card">
        <div class="topic-card-inner">
            <div class="topic-icon">
                <i class="fa fa-exclamation-triangle"></i>
            </div>
            <div class="topic-content">
                <h3 class="topic-title">{{ $tag->tag_text }}</h3>
                @if ($tag->overview)
                    <p class="topic-description">
                        {{ plain_text_excerpt_from_html($tag->overview, 100) }}
                    </p>
                @else
                    <p class="topic-description text-muted">
                        Click to view resources and publications
                    </p>
                @endif
            </div>
            <div class="topic-action">
                <span class="action-btn">
                    View <i class="fa fa-arrow-right"></i>
                </span>
            </div>
        </div>
    </a>
@endforeach
