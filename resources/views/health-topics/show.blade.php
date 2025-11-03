@php 
    $primary = settings()->primary_color ?? '#119A48';
    $textColor = settings()->links_active_color ?? settings()->primary_text_color ?? $primary;
@endphp
@extends('layouts.app')

@section('title', $tag->tag_text . ' - Health Topics')

@section('styles')
<style>
    /* Prevent overview overflow */
    .overview-content {
        word-wrap: break-word;
        overflow-wrap: break-word;
        max-height: none;
        overflow: visible;
    }
    
    /* Ensure proper text wrapping in overview */
    .sidebar-content {
        overflow: visible;
    }
    
    /* Prevent text overflow in overview section */
    .overview-text-container {
        word-wrap: break-word;
        overflow-wrap: break-word;
        hyphens: auto;
        overflow-x: hidden;
        max-width: 100%;
        box-sizing: border-box;
    }
    
    /* Handle long URLs and video embeds */
    .overview-text-container a {
        word-break: break-all;
        overflow-wrap: break-word;
    }
    
    .overview-text-container .video-preview-inline {
        max-width: 100%;
        overflow: hidden;
    }
    
    .overview-text-container .video-preview-inline iframe,
    .overview-text-container .video-preview-inline video {
        max-width: 100%;
        height: auto;
    }
    
    /* Ensure main content column layout */
    @media (min-width: 992px) {
        .col-lg-7 {
            flex: 0 0 60%;
            max-width: 60%;
        }
        .col-lg-5 {
            flex: 0 0 40%;
            max-width: 40%;
        }
    }
</style>
@endsection

@section('content')
<div class="gray py-4">
    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <div class="col-md-8">
                <!-- Topic Header & Description -->
                <div class="mb-4">
                    @if($tag->overview)
                        <div class="sidebar-content" style="background:#fff;border:1px solid #e2e8f0;border-radius:0.25rem;padding:18px;box-shadow:0 2px 8px rgba(0,0,0,.04);">
                            <div class="d-flex align-items-center justify-content-between mb-3" style="flex-wrap: wrap; gap: 10px;">
                                <h1 class="fw-bold" style="font-size: 2rem; color: var(--theme-color-primary, {{ $primary }}); margin-bottom: 1rem;">
                                    {{ $tag->tag_text }}
                                </h1>
                                <span class="badge" style="background-color: var(--theme-color-primary, {{ $primary }}); color: white; padding: 0.4rem 0.8rem; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 500;">
                                    {{ $tag->tag_text }}
                                </span>
                            </div>
                            <div class="overview-text-container" style="font-size: 1rem; line-height: 1.7; color: #4a5568; word-wrap: break-word; overflow-wrap: break-word; overflow-x: hidden; max-width: 100%;">
                                {!! detect_and_embed_video_links($tag->overview, 180, 180) !!}
                            </div>
                        </div>
                    @endif
                </div>
          
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <style>
                    .sidebar-content{background:#fff;border:1px solid #e2e8f0;border-radius:0.25rem;padding:18px;box-shadow:0 2px 8px rgba(0,0,0,.04);margin-bottom:20px}
                    .sidebar-content h5{margin-bottom:15px;font-size:15px;font-weight:600;color:#2d3748}
                    .sidebar-content .btn-outline-primary{color:var(--theme-color-primary, {{ $primary }});border-color:var(--theme-color-primary, {{ $primary }});width:100%}
                    .sidebar-content .btn-outline-primary:hover{background:var(--theme-color-primary, {{ $primary }});color:#fff}
                    .forum-item{margin-bottom:15px;padding-bottom:15px;border-bottom:1px solid #e2e8f0}
                    .forum-item:last-child{margin-bottom:0;padding-bottom:0;border-bottom:none}
                    .forum-item-title{font-size:0.9rem;font-weight:600;color:#0f172a;line-height:1.4;margin-bottom:8px}
                    .forum-item-title a{color:{{ $textColor }};text-decoration:none;transition:color 0.2s}
                    .forum-item-title a:hover{color:var(--theme-color-primary, {{ $primary }})}
                    .forum-item-meta{font-size:0.75rem;color:#94a3b8;display:flex;align-items:center;gap:12px;flex-wrap:wrap}
                    .forum-item-meta i{color:var(--theme-color-primary, {{ $primary }})}
                    .community-item{margin-bottom:15px;padding-bottom:15px;border-bottom:1px solid #e2e8f0}
                    .community-item:last-child{margin-bottom:0;padding-bottom:0;border-bottom:none}
                    .community-item-title{font-size:0.9rem;font-weight:600;color:#0f172a;line-height:1.4;margin-bottom:8px}
                    .community-item-title a{color:{{ $textColor }};text-decoration:none;transition:color 0.2s}
                    .community-item-title a:hover{color:var(--theme-color-primary, {{ $primary }})}
                    .community-item-meta{font-size:0.75rem;color:#94a3b8;display:flex;align-items:center;gap:12px;flex-wrap:wrap}
                    .community-item-meta i{color:var(--theme-color-primary, {{ $primary }})}
                    .sidebar-publication-item{margin-bottom:15px;padding-bottom:15px;border-bottom:1px solid #e2e8f0}
                    .sidebar-publication-item:last-child{margin-bottom:0;padding-bottom:0;border-bottom:none}
                    .sidebar-publication-title{font-size:0.9rem;font-weight:600;color:#0f172a;line-height:1.4;margin-bottom:8px}
                    .sidebar-publication-title a{color:{{ $textColor }};text-decoration:none;transition:color 0.2s}
                    .sidebar-publication-title a:hover{color:var(--theme-color-primary, {{ $primary }})}
                    .sidebar-publication-desc{font-size:0.8rem;color:#64748b;margin-bottom:8px;line-height:1.5}
                    .sidebar-publication-meta{font-size:0.75rem;color:#94a3b8;display:flex;align-items:center;gap:12px;flex-wrap:wrap}
                    .sidebar-publication-meta i{color:var(--theme-color-primary, {{ $primary }})}
                </style>

                <!-- Topic Information -->
                <div class="sidebar-content">
                    <h5><i class="fa fa-info-circle me-2" style="color:var(--theme-color-primary, {{ $primary }});"></i>Topic Information</h5>
                    <div style="font-size:0.875rem;color:#64748b;line-height:1.6;">
                        @if(isset($relatedCommunities))
                        <p class="mb-2"><strong>Related Communities:</strong> {{ $relatedCommunities->count() }}</p>
                        @endif
                        @if(isset($relatedForums))
                        <p class="mb-0"><strong>Related Forums:</strong> {{ $relatedForums->count() }}</p>
                        @endif
                    </div>
                </div>

                <!-- Related Communities -->
                @if(isset($relatedCommunities) && $relatedCommunities->count() > 0)
                <div class="sidebar-content">
                    <h5><i class="fa fa-users me-2" style="color:var(--theme-color-primary, {{ $primary }});"></i>Related Communities</h5>
                    @foreach($relatedCommunities->take(5) as $community)
                    <div class="community-item">
                        <div class="community-item-title">
                            <a href="{{ url('communities/detail/' . $community->id) }}">
                                {{ Str::limit(strip_tags($community->community_name), 80) }}
                            </a>
                        </div>
                        <div class="community-item-meta">
                            <span><i class="fa fa-users"></i> {{ $community->members_count ?? 0 }} {{ ($community->members_count ?? 0) == 1 ? 'member' : 'members' }}</span>
                            <span><i class="fa fa-comments"></i> {{ $community->forums_count ?? 0 }} {{ ($community->forums_count ?? 0) == 1 ? 'forum' : 'forums' }}</span>
                            @if($community->created_at)
                            <span><i class="fa fa-clock"></i> {{ $community->created_at->diffForHumans() }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    <a href="{{ route('community.index') }}?tag={{ $tag->tag_text }}" class="btn btn-sm btn-outline-primary mt-2">View All Communities</a>
                </div>
                @endif

                <!-- Publications Count -->
                @if($publications->count() > 0)
                <div class="sidebar-content mb-3" style="background:#fff;border:1px solid #e2e8f0;border-radius:0.25rem;padding:18px;box-shadow:0 2px 8px rgba(0,0,0,.04);">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 me-4" style="color:var(--theme-color-primary, {{ $primary }});">Publications</h5>
                        <span class="fw-bold" style="color:#1e293b;font-size:1rem;">
                            {{ $publications->total() }} {{ $publications->total() == 1 ? 'publication' : 'publications' }} found
                        </span>
                    </div>
                </div>
                @endif

                <!-- Publications List -->
                @if($publications->count() > 0)
                    @include('health-topics.partials.publications')
                    
                    <!-- Pagination -->
                    @if($publications->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $publications->links() }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="sidebar-content text-center py-5" style="background:#fff;border:1px solid #e2e8f0;border-radius:0.25rem;padding:18px;box-shadow:0 2px 8px rgba(0,0,0,.04);">
                        <i class="fa fa-file-alt fa-2x text-muted mb-3"></i>
                        <h5>No Publications Found</h5>
                        <p class="text-secondary">There are currently no publications tagged with <strong>"{{ $tag->tag_text }}"</strong>.</p>
                        <a href="{{ url('records') }}" class="btn btn-outline-primary mt-2">Browse All Publications</a>
                    </div>
                @endif

                <!-- Related Forums -->
                @if(isset($relatedForums) && $relatedForums->count() > 0)
                <div class="sidebar-content">
                    <h5><i class="fa fa-comments me-2" style="color:var(--theme-color-primary, {{ $primary }});"></i>Related Forums</h5>
                    @foreach($relatedForums->take(5) as $forum)
                    <div class="forum-item">
                        <div class="forum-item-title">
                            <a href="{{ url('forums/thread?id=' . $forum->id) }}">
                                {{ Str::limit(strip_tags($forum->forum_title), 80) }}
                            </a>
                        </div>
                        <div class="forum-item-meta">
                            @if($forum->user)
                            <span><i class="fa fa-user"></i> {{ $forum->user->name ?? 'Anonymous' }}</span>
                            @endif
                            <span><i class="fa fa-comments"></i> {{ $forum->total_comments ?? 0 }} {{ ($forum->total_comments ?? 0) == 1 ? 'comment' : 'comments' }}</span>
                            @if($forum->created_at)
                            <span><i class="fa fa-clock"></i> {{ $forum->created_at->diffForHumans() }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    <a href="{{ url('forums') }}?tag={{ $tag->tag_text }}" class="btn btn-sm btn-outline-primary mt-2">View All Forums</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
