@extends('layouts.app')

@section('styles')
<style>
    .contrib-stat-card {
        background: #fff;
        border: 1px solid #e8eaed;
        border-radius: 6px;
        padding: 0.85rem 1rem;
        margin-bottom: 0.75rem;
    }
    .contrib-stat-title {
        color: #6c757d;
        font-size: 0.82rem;
        margin-bottom: 0.15rem;
    }
    .contrib-stat-value {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1f2937;
        line-height: 1.2;
    }
    .forum-contrib-card {
        background: #fff;
        border: 1px solid #e8eaed;
        border-radius: 6px;
        padding: 0.95rem 1rem;
        margin-bottom: 0.8rem;
    }
    .forum-contrib-meta {
        font-size: 0.85rem;
        color: #6c757d;
    }
</style>
@endsection

@section('content')
<div class="gray py-4">
<div class="container">
	<div class="row">

     <div class="col-lg-8">
     	<div class="row">
		 <h4>Source: 
		 @if(!empty($author->orcid))
		     <a href="https://orcid.org/{{ $author->orcid }}" target="_blank" rel="noopener noreferrer" title="View {{ $author->name }}'s ORCID profile">
		         {{ $author->name }}
		         <i class="fa fa-external-link-alt" style="font-size: 0.8em; margin-left: 5px;"></i>
		     </a>
		 @else
		     {{ $author->name }}
		 @endif
		 </h4>
     	</div>
        <div class="row mb-2">
            <div class="col-md-3 col-6">
                <div class="contrib-stat-card">
                    <div class="contrib-stat-title">Total Contributions</div>
                    <div class="contrib-stat-value">{{ number_format((int) ($contributionStats['total_contributions'] ?? 0)) }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="contrib-stat-card">
                    <div class="contrib-stat-title">Resource Contributions</div>
                    <div class="contrib-stat-value">{{ number_format((int) ($contributionStats['resource_contributions'] ?? 0)) }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="contrib-stat-card">
                    <div class="contrib-stat-title">Forum Threads</div>
                    <div class="contrib-stat-value">{{ number_format((int) ($contributionStats['forum_posts'] ?? 0)) }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="contrib-stat-card">
                    <div class="contrib-stat-title">Forum Comments</div>
                    <div class="contrib-stat-value">{{ number_format((int) ($contributionStats['forum_comments'] ?? 0)) }}</div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <h5 class="mb-3">Forum Contributions</h5>
                @if(isset($forumContributions) && method_exists($forumContributions, 'count') && $forumContributions->count() > 0)
                    @foreach($forumContributions as $forum)
                        <div class="forum-contrib-card">
                            <div class="d-flex justify-content-between align-items-start flex-wrap">
                                <a href="{{ forum_thread_url($forum) }}" class="fw-bold text-dark mb-1">
                                    {!! strip_tags($forum->forum_title ?? 'Untitled discussion') !!}
                                </a>
                                <div>
                                    @if(!empty($forum->is_authored_by_contributor))
                                        <span class="badge badge-success">Started by contributor</span>
                                    @endif
                                    @if((int) ($forum->my_comment_count ?? 0) > 0)
                                        <span class="badge badge-info">Comments by contributor: {{ (int) $forum->my_comment_count }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="forum-contrib-meta mt-1">
                                <i class="fa fa-comments-o mr-1"></i>{{ (int) ($forum->total_comments ?? 0) }} comments
                                <span class="mx-2">|</span>
                                <i class="fa fa-thumbs-up mr-1"></i>{{ (int) ($forum->total_likes ?? 0) }} likes
                            </div>
                        </div>
                    @endforeach

                    @if(method_exists($forumContributions, 'hasPages') && $forumContributions->hasPages())
                        <div class="mt-2">
                            {{ $forumContributions->appends(request()->except('forums_page'))->links() }}
                        </div>
                    @endif
                @else
                    <div class="alert alert-light border">No forum contributions found for this contributor.</div>
                @endif
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <h5 class="mb-3">Resource Contributions</h5>
            </div>
        </div>

	    @include('publications.partials.publications')
       
	</div>
	<div class="col-lg-4">
    @include('publications.partials.facts')
    </div>
</div>
</div>
</div>
@endsection