@php 
$primary = settings()->primary_color ?? '#119A48';
$totalTopics = $groupedTags->flatten()->count();
@endphp
@extends('layouts.app')

@section('title', 'Health Topics')

@section('content')
<div class="health-topics-wrapper">
    <div class="container" style="max-width: 1200px;">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-title-section">
                    <h1><i class="fa fa-stethoscope me-2"></i>Health Topics</h1>
                    <p class="header-subtitle">Explore health emergency topics and access relevant resources and publications</p>
                </div>
                <div class="header-stats">
                    <div class="stat-badge">
                        <span class="stat-number">{{ $totalTopics }}</span>
                        <span class="stat-label">Topics</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <div class="search-wrapper">
                <i class="fa fa-search search-icon"></i>
                <input type="text" 
                       id="topic-search" 
                       class="search-input" 
                       placeholder="Search health topics by name...">
                <button class="search-clear" id="clear-search" style="display: none;">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Alphabetical Filter -->
        @if($groupedTags->count() > 0)
        <div class="alphabet-filter">
            <div class="filter-label">Filter by letter:</div>
            <div class="filter-letters">
                @foreach($groupedTags->keys()->sort() as $letter)
                    <a href="#letter-{{ $letter }}" class="filter-letter" data-letter="{{ $letter }}">
                        {{ $letter }}
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Health Topics Content -->
        <div class="topics-content">
            @if($groupedTags->count() > 0)
                @foreach($groupedTags->sortKeys() as $letter => $tags)
                    <div class="letter-group" id="letter-{{ $letter }}" data-letter="{{ $letter }}">
                        <div class="letter-header">
                            <h2 class="letter-title">{{ $letter }}</h2>
                            <span class="letter-count">{{ $tags->count() }} {{ $tags->count() === 1 ? 'topic' : 'topics' }}</span>
                        </div>
                        <div class="topics-grid">
                            @foreach($tags as $tag)
                                <a href="{{ route('health-topics.show', $tag->id) }}" class="topic-card">
                                    <div class="topic-card-inner">
                                        <div class="topic-icon">
                                            <i class="fa fa-exclamation-triangle"></i>
                                        </div>
                                        <div class="topic-content">
                                            <h3 class="topic-title">{{ $tag->tag_text }}</h3>
                                            @if($tag->overview)
                                                <p class="topic-description">
                                                    {{ Str::limit(strip_tags($tag->overview), 100) }}
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
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fa fa-info-circle"></i>
                    </div>
                    <h3>No Health Topics Available</h3>
                    <p>There are currently no health emergency topics configured in the system.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.health-topics-wrapper {
    background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
    min-height: calc(100vh - 100px);
    padding: 2rem 0 3rem;
}

.page-header {
    background: white;
    border-radius: 16px;
    padding: 2.5rem 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 2rem;
}

.header-title-section h1 {
    font-size: 2rem;
    font-weight: 700;
    color: var(--theme-color-primary, #119A48);
    margin: 0 0 0.5rem 0;
    letter-spacing: -0.5px;
}

.header-subtitle {
    color: #64748b;
    margin: 0;
    font-size: 1.05rem;
    line-height: 1.6;
}

.header-stats {
    display: flex;
    gap: 1rem;
}

.stat-badge {
    background: linear-gradient(135deg, var(--theme-color-primary, #119A48) 0%, color-mix(in srgb, var(--theme-color-primary, #119A48) 85%, black) 100%);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    text-align: center;
    min-width: 120px;
    box-shadow: 0 4px 12px rgba(17, 154, 72, 0.2);
}

.stat-number {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
}

.stat-label {
    display: block;
    font-size: 0.875rem;
    opacity: 0.95;
    margin-top: 0.5rem;
}

.search-section {
    margin-bottom: 2rem;
}

.search-wrapper {
    position: relative;
    background: white;
    border-radius: 12px;
    padding: 1rem 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
}

.search-icon {
    color: #94a3b8;
    font-size: 1.2rem;
    margin-right: 1rem;
}

.search-input {
    flex: 1;
    border: none;
    outline: none;
    font-size: 1rem;
    color: #1e293b;
    background: transparent;
}

.search-input::placeholder {
    color: #94a3b8;
}

.search-clear {
    background: #f1f5f9;
    border: none;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #64748b;
    transition: all 0.2s ease;
}

.search-clear:hover {
    background: #e2e8f0;
    color: var(--theme-color-primary, #119A48);
}

.alphabet-filter {
    background: white;
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.filter-label {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.75rem;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-letters {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.filter-letter {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: #f8f9fa;
    color: #64748b;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    border: 2px solid transparent;
}

.filter-letter:hover,
.filter-letter.active {
    background: var(--theme-color-primary, #119A48);
    color: white;
    border-color: var(--theme-color-primary, #119A48);
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(17, 154, 72, 0.3);
    text-decoration: none;
}

.topics-content {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.letter-group {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.letter-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e2e8f0;
}

.letter-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--theme-color-primary, #119A48);
    margin: 0;
}

.letter-count {
    background: #f1f5f9;
    color: #64748b;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

.topics-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1rem; }

.topic-card { display:block; background:#fff; border:2px solid #e2e8f0; border-radius:12px; padding:1rem; transition:all .3s ease; text-decoration:none; color:inherit; position:relative; overflow:hidden; }

.topic-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--theme-color-primary, #119A48), var(--theme-color-secondary, #0d7a3a));
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.topic-card:hover {
    border-color: var(--theme-color-primary, #119A48);
    box-shadow: 0 8px 24px rgba(17, 154, 72, 0.15);
    transform: translateY(-4px);
    text-decoration: none;
    color: inherit;
}

.topic-card:hover::before {
    transform: scaleX(1);
}

.topic-card-inner { display:flex; flex-direction:column; gap:.75rem; }

.topic-icon { width:40px; height:40px;
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(17, 154, 72, 0.1) 0%, rgba(17, 154, 72, 0.05) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--theme-color-primary, #119A48);
    font-size: 1.1rem;
}

.topic-card:hover .topic-icon {
    background: linear-gradient(135deg, var(--theme-color-primary, #119A48) 0%, var(--theme-color-secondary, #0d7a3a) 100%);
    color: white;
    transform: scale(1.1);
}

.topic-content {
    flex: 1;
}

.topic-title { font-size:1.05rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.35rem 0; line-height:1.25; }

.topic-description { font-size:.85rem; color:#64748b; margin:0; line-height:1.45; }

.topic-action {
    display: flex;
    align-items: center;
    justify-content: flex-end;
}

.action-btn { display:inline-flex; align-items:center; gap:.35rem; padding:.35rem .6rem; background:var(--theme-color-primary, #119A48); color:#fff; border-radius:8px; font-weight:600; font-size:.82rem; transition:all .2s ease; }

.topic-card:hover .action-btn {
    background: var(--theme-color-secondary, #0d7a3a);
    transform: translateX(4px);
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

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #64748b;
    font-size: 1rem;
}

/* Responsive */
@media (max-width: 991px) {
    .header-content {
        flex-direction: column;
    }

    .stat-badge {
        align-self: flex-start;
    }

    .topics-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
    }
}

@media (max-width: 767px) {
    .health-topics-wrapper {
        padding: 1rem 0 2rem;
    }

    .page-header {
        padding: 1.5rem;
    }

    .header-title-section h1 {
        font-size: 1.5rem;
    }

    .topics-grid {
        grid-template-columns: 1fr;
    }

    .letter-group {
        padding: 1.5rem;
    }

    .letter-title {
        font-size: 1.5rem;
    }
}

/* Hidden class for search filtering */
.letter-group.hidden,
.topic-card.hidden {
    display: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('topic-search');
    const clearBtn = document.getElementById('clear-search');
    const filterLetters = document.querySelectorAll('.filter-letter');
    const topicCards = document.querySelectorAll('.topic-card');
    const letterGroups = document.querySelectorAll('.letter-group');

    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.trim().toLowerCase();
        
        if (searchTerm.length > 0) {
            clearBtn.style.display = 'flex';
        } else {
            clearBtn.style.display = 'none';
        }

        let hasResults = false;

        topicCards.forEach(card => {
            const title = card.querySelector('.topic-title').textContent.toLowerCase();
            const description = card.querySelector('.topic-description').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || description.includes(searchTerm)) {
                card.classList.remove('hidden');
                card.closest('.letter-group').classList.remove('hidden');
                hasResults = true;
            } else {
                card.classList.add('hidden');
            }
        });

        // Hide letter groups with no visible cards
        letterGroups.forEach(group => {
            const visibleCards = group.querySelectorAll('.topic-card:not(.hidden)');
            if (visibleCards.length === 0) {
                group.classList.add('hidden');
            }
        });
    });

    // Clear search
    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        clearBtn.style.display = 'none';
        topicCards.forEach(card => card.classList.remove('hidden'));
        letterGroups.forEach(group => group.classList.remove('hidden'));
        filterLetters.forEach(letter => letter.classList.remove('active'));
    });

    // Alphabet filter
    filterLetters.forEach(letterLink => {
        letterLink.addEventListener('click', function(e) {
            e.preventDefault();
            const letter = this.dataset.letter;
            
            // Update active state
            filterLetters.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            // Scroll to letter group
            const targetGroup = document.getElementById(`letter-${letter}`);
            if (targetGroup) {
                const offset = 100;
                const elementPosition = targetGroup.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - offset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });

                // Highlight briefly
                targetGroup.style.boxShadow = '0 4px 16px rgba(17, 154, 72, 0.3)';
                setTimeout(() => {
                    targetGroup.style.boxShadow = '';
                }, 1000);
            }
        });
    });

    // Auto-scroll to active letter on page load if hash exists
    if (window.location.hash) {
        const target = document.querySelector(window.location.hash);
        if (target) {
            setTimeout(() => {
                const offset = 100;
                const elementPosition = target.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - offset;
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }, 500);
        }
    }
});
</script>
@endsection