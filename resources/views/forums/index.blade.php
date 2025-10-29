@extends('layouts.app')

@section('title', 'Discussions & Forums')

@section('styles')
<style>
.forums-wrapper {
    background: #f4f5f7;
    min-height: calc(100vh - 200px);
    padding: 2rem 0;
}

.forums-header {
    background: linear-gradient(135deg, var(--theme-color-primary, #119A48) 0%, #16c653 100%);
    color: white;
    padding: 3rem 2rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    box-shadow: 0 8px 24px rgba(17, 154, 72, 0.2);
}

.forums-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
}

.forums-header p {
    font-size: 1.1rem;
    opacity: 0.95;
    margin: 0;
}

.forums-filters {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.search-bar {
    position: relative;
    margin-bottom: 1rem;
}

.search-bar input {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 3rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.search-bar input:focus {
    border-color: var(--theme-color-primary, #119A48);
    box-shadow: 0 0 0 3px rgba(17, 154, 72, 0.1);
    outline: none;
}

.search-bar .search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 1.1rem;
}

.filter-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.filter-btn {
    padding: 0.5rem 1rem;
    border: 2px solid #e2e8f0;
    background: white;
    border-radius: 8px;
    color: #64748b;
    font-weight: 500;
    transition: all 0.2s ease;
    cursor: pointer;
    font-size: 0.9rem;
}

.filter-btn:hover,
.filter-btn.active {
    background: var(--theme-color-primary, #119A48);
    border-color: var(--theme-color-primary, #119A48);
    color: white;
}

.forum-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.forum-card:hover {
    box-shadow: 0 8px 24px rgba(17, 154, 72, 0.15);
    transform: translateY(-4px);
    border-color: var(--theme-color-primary, #119A48);
}

.forum-header {
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
    margin-bottom: 1rem;
}

.forum-image {
    width: 120px;
    height: 120px;
    border-radius: 12px;
    object-fit: cover;
    flex-shrink: 0;
}

.forum-content {
    flex: 1;
}

.forum-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.75rem 0;
    line-height: 1.3;
}

.forum-title a {
    color: inherit;
    text-decoration: none;
    transition: color 0.2s ease;
}

.forum-title a:hover {
    color: var(--theme-color-primary, #119A48);
    text-decoration: none;
}

.forum-description {
    color: #64748b;
    line-height: 1.6;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.forum-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #64748b;
    font-size: 0.9rem;
}

.meta-item i {
    color: var(--theme-color-primary, #119A48);
}

.forum-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin: 1rem 0;
}

.tag {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    background: rgba(17, 154, 72, 0.1);
    color: var(--theme-color-primary, #119A48);
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.forum-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 1rem;
}

.btn-join,
.btn-view {
    padding: 0.625rem 1.25rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-join {
    background: var(--theme-color-primary, #119A48);
    color: white;
    border: none;
}

.btn-join:hover {
    background: color-mix(in srgb, var(--theme-color-primary, #119A48) 85%, black);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(17, 154, 72, 0.3);
}

.btn-view {
    background: white;
    color: var(--theme-color-primary, #119A48);
    border: 2px solid var(--theme-color-primary, #119A48);
}

.btn-view:hover {
    background: var(--theme-color-primary, #119A48);
    color: white;
    text-decoration: none;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.empty-icon {
    font-size: 4rem;
    color: #cbd5e1;
    margin-bottom: 1rem;
}

.stats-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    font-weight: 600;
}

@media (max-width: 768px) {
    .forums-header {
        padding: 2rem 1.5rem;
    }

    .forums-header h1 {
        font-size: 1.75rem;
    }

    .forum-header {
        flex-direction: column;
    }

    .forum-image {
        width: 100%;
        height: 200px;
    }

    .forum-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
}
</style>
@endsection

@section('content')
<div class="forums-wrapper">
    <div class="container" style="max-width: 1200px;">
        <!-- Header -->
        <div class="forums-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1><i class="fa fa-comments me-2"></i>Discussions & Forums</h1>
                    <p>Join conversations, share knowledge, and collaborate with the community</p>
                            </div>
                @auth
                <a href="{{ url('forums/create') }}" class="btn btn-light btn-lg" style="font-weight: 600;">
                    <i class="fa fa-plus-circle me-2"></i>Start New Discussion
                            </a>
                            @endauth
                        </div>
            <div class="mt-3">
                <span class="stats-badge">
                    <i class="fa fa-comment-dots"></i>
                    {{ $forums->total() }} {{ $forums->total() === 1 ? 'Discussion' : 'Discussions' }}
                </span>
                    </div>
                </div>

        <!-- Filters -->
        <div class="forums-filters">
            <div class="search-bar">
                <i class="fa fa-search search-icon"></i>
                <input type="text" id="forum-search" placeholder="Search discussions by title, description, or tags...">
            </div>
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">All Discussions</button>
                <button class="filter-btn" data-filter="joined">My Discussions</button>
                <button class="filter-btn" data-filter="recent">Most Recent</button>
                <button class="filter-btn" data-filter="popular">Most Active</button>
            </div>
        </div>

        <!-- Forums List -->
        <div id="forums-list">
            @forelse($forums as $forum)
                <div class="forum-card" 
                     data-forum-id="{{ $forum->id }}"
                     data-joined="{{ in_array($forum->id, $my_forums) ? 'true' : 'false' }}"
                     data-comments="{{ count($forum->comments) }}"
                     data-date="{{ $forum->created_at }}">
                    <div class="forum-header">
                        @if($forum->forum_image)
                        <img src="{{ $forum->forum_image }}" alt="{{ $forum->forum_title }}" class="forum-image">
                        @else
                        <div class="forum-image" style="background: linear-gradient(135deg, rgba(17, 154, 72, 0.1) 0%, rgba(17, 154, 72, 0.05) 100%); display: flex; align-items: center; justify-content: center; color: var(--theme-color-primary, #119A48); font-size: 3rem;">
                            <i class="fa fa-comments"></i>
                        </div>
                        @endif

                        <div class="forum-content">
                            <h2 class="forum-title">
                                @if(in_array($forum->id, $my_forums))
                                    <a href="{{ url('forums/thread') }}?id={{ $forum->id }}">{!! $forum->forum_title !!}</a>
                                @else
                                    {!! $forum->forum_title !!}
                                @endif
                            </h2>
                            <p class="forum-description">{!! Str::limit(strip_tags($forum->forum_description), 200) !!}</p>

                            @if(count($forum->tags) > 0)
                            <div class="forum-tags">
                                @foreach($forum->tags as $tag)
                                <span class="tag">#{{ $tag->tag }}</span>
                                @endforeach
                            </div>
                            @endif

                            <div class="forum-meta">
                                <div class="meta-item">
                                    <i class="fa fa-user"></i>
                                    <span>{{ $forum->user->name ?? 'Unknown' }}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fa fa-clock"></i>
                                    <span>{{ time_ago($forum->created_at) }}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fa fa-comments"></i>
                                    <span>{{ count($forum->comments) }} {{ count($forum->comments) === 1 ? 'Comment' : 'Comments' }}</span>
                                </div>
                                </div>

                            <div class="forum-actions">
                                @if(in_array($forum->id, $my_forums))
                                    <a href="{{ url('forums/thread') }}?id={{ $forum->id }}" class="btn-view">
                                        <i class="fa fa-eye"></i> View Discussion
                                    </a>
                                    @else
                                    <a href="{{ url('forums/join') }}?id={{ $forum->id }}" class="btn-join" id="join{{ $forum->id }}">
                                        <i class="fa fa-link"></i> Join Discussion
                                    </a>
                                    @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fa fa-comments"></i>
                    </div>
                    <h3>No Discussions Yet</h3>
                    <p class="text-muted">Be the first to start a discussion!</p>
                    @auth
                    <a href="{{ url('forums/create') }}" class="btn btn-primary mt-3">
                        <i class="fa fa-plus-circle me-2"></i>Start First Discussion
                    </a>
                    @endauth
                </div>
            @endforelse
            </div>

        <!-- Pagination -->
        @if($forums->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $forums->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('forum-search');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const forumCards = document.querySelectorAll('.forum-card');

    // Search functionality
    if(searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim().toLowerCase();
            filterForums(searchTerm);
        });
    }

    // Filter buttons
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const filter = this.dataset.filter;
            applyFilter(filter);
        });
    });

    function filterForums(searchTerm) {
        forumCards.forEach(card => {
            const title = card.querySelector('.forum-title').textContent.toLowerCase();
            const description = card.querySelector('.forum-description').textContent.toLowerCase();
            const tags = Array.from(card.querySelectorAll('.tag')).map(t => t.textContent.toLowerCase()).join(' ');
            
            if (!searchTerm || title.includes(searchTerm) || description.includes(searchTerm) || tags.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function applyFilter(filter) {
        const searchTerm = searchInput ? searchInput.value.trim().toLowerCase() : '';
        
        forumCards.forEach(card => {
            let shouldShow = true;
            
            if (filter === 'joined') {
                shouldShow = card.dataset.joined === 'true';
            } else if (filter === 'recent') {
                // Already sorted by recent by default
                shouldShow = true;
            } else if (filter === 'popular') {
                const comments = parseInt(card.dataset.comments) || 0;
                shouldShow = comments > 0;
            }
            
            // Also apply search filter
            if (shouldShow && searchTerm) {
                const title = card.querySelector('.forum-title').textContent.toLowerCase();
                const description = card.querySelector('.forum-description').textContent.toLowerCase();
                const tags = Array.from(card.querySelectorAll('.tag')).map(t => t.textContent.toLowerCase()).join(' ');
                shouldShow = title.includes(searchTerm) || description.includes(searchTerm) || tags.includes(searchTerm);
            }
            
            card.style.display = shouldShow ? 'block' : 'none';
        });
    }
});
</script>
@endsection
