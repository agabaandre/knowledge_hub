@extends('layouts.app')

@section('styles')
@endsection

@section('content')
    <div class="gray py-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="row mb-3">
                        <div class="col-12">
                            <h4 class="mb-3">Search Results</h4>
                            @if(isset($_GET['tag']) && !empty($_GET['tag']))
                                @php
                                    $tagId = $_GET['tag'];
                                    $tag = \App\Models\Tag::find($tagId);
                                    $tagName = $tag ? $tag->tag_text : 'Tag #' . $tagId;
                                @endphp
                                <div class="alert alert-info mb-3">
                                    <i class="fa fa-tag me-2"></i>
                                    <strong>Tag:</strong> {{ $tagName }}
                                </div>
                            @endif
                        </div>
                    </div>

                    @include('partials.quiz.quiz')

                    @if (count($sub_themes) > 0)
                        @include('publications.partials.subthemes')
                    @endif

                    @include('publications.partials.publications')

                </div>

                <div class="col-lg-4">
                    <style>
                        .sidebar-content{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:18px;box-shadow:0 2px 8px rgba(0,0,0,.04);margin-bottom:20px}
                        .sidebar-content h5{color:var(--theme-color-primary, #119A48);margin-bottom:15px}
                        .sidebar-content .btn-outline-primary{color:var(--theme-color-primary, #119A48);border-color:var(--theme-color-primary, #119A48)}
                        .sidebar-content .btn-outline-primary:hover{background:var(--theme-color-primary, #119A48);color:#fff}
                        .sidebar-tags{display:flex;flex-wrap:wrap;gap:0.5rem}
                        .sidebar-tag-pill{display:inline-block;padding:0.3rem 0.7rem;font-size:0.8rem;font-weight:500;color:#ffffff !important;text-decoration:none;border-radius:12px;transition:all 0.2s ease;white-space:nowrap;background-color:var(--theme-color-primary, #119A48) !important;border:1px solid rgba(17,154,72,0.3)}
                        .sidebar-tag-pill:hover{transform:translateY(-2px);box-shadow:0 2px 6px rgba(17,154,72,0.3);color:#ffffff !important;text-decoration:none;background-color:var(--theme-color-primary, #119A48) !important}
                    </style>

                    {{-- Popular Tags --}}
                    @if(isset($tags) && count($tags) > 0)
                    <div class="sidebar-content">
                      <h5 class="mb-3">Popular Tags</h5>
                      <div class="sidebar-tags">
                        @foreach($tags->take(10) as $tag)
                        <a href="{{ url('records')}}?tag={{$tag->id}}" 
                           class="sidebar-tag-pill" 
                           title="{{$tag->tag_text}}">
                          {{ truncate($tag->tag_text,15) }}
                        </a>
                        @endforeach
                      </div>
                    </div>
                    @endif

                    {{-- Related Resources --}}
                    @if(isset($relatedPublications) && $relatedPublications->count() > 0)
                    <div class="sidebar-content">
                      <h5 class="mb-3">Related Resources</h5>
                      @foreach($relatedPublications->take(5) as $pub)
                      <div class="mb-3 pb-3 border-bottom">
                        <a href="{{ url('records/resource?id=' . $pub->id) }}" class="text-decoration-none">
                          <h6 class="mb-1" style="font-size:0.9rem;color:#0f172a;line-height:1.4;">{{ Str::limit(strip_tags($pub->title), 80) }}</h6>
                        </a>
                        <p class="mb-1" style="font-size:0.8rem;color:#64748b;">{{ Str::limit(strip_tags($pub->description ?? ''), 100) }}</p>
                        <div class="d-flex align-items-center" style="font-size:0.75rem;color:#94a3b8;">
                          @if($pub->author)
                          <span class="mr-2"><i class="fa fa-user mr-1"></i>{{ $pub->author->name }}</span>
                          @endif
                          <span><i class="fa fa-calendar mr-1"></i>{{ $pub->created_at->format('M Y') }}</span>
                        </div>
                      </div>
                      @endforeach
                      <a href="{{ url('records') }}" class="btn btn-sm btn-outline-primary btn-block mt-2">View All Resources</a>
                    </div>
                    @endif

                    {{-- Latest Publications --}}
                    @if(isset($latestPublications) && $latestPublications->count() > 0)
                    <div class="sidebar-content">
                      <h5 class="mb-3">Latest Publications</h5>
                      @foreach($latestPublications->take(5) as $pub)
                      <div class="mb-3 pb-3 border-bottom">
                        <a href="{{ url('records/resource?id=' . $pub->id) }}" class="text-decoration-none">
                          <h6 class="mb-1" style="font-size:0.9rem;color:#0f172a;line-height:1.4;">{{ Str::limit(strip_tags($pub->title), 80) }}</h6>
                        </a>
                        <p class="mb-1" style="font-size:0.8rem;color:#64748b;">{{ Str::limit(strip_tags($pub->description ?? ''), 100) }}</p>
                        <div class="d-flex align-items-center" style="font-size:0.75rem;color:#94a3b8;">
                          @if($pub->author)
                          <span class="mr-2"><i class="fa fa-user mr-1"></i>{{ $pub->author->name }}</span>
                          @endif
                          <span><i class="fa fa-calendar mr-1"></i>{{ $pub->created_at->format('M Y') }}</span>
                        </div>
                      </div>
                      @endforeach
                      <a href="{{ url('records') }}" class="btn btn-sm btn-outline-primary btn-block mt-2">View All Publications</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        @include('common.select2')
    @endsection
