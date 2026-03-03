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
                            {{-- Search Results Info Card --}}
                            @if(isset($results_count) || isset($search_time))
                            <div class="sidebar-content mb-3" style="background:#fff;border:1px solid #e2e8f0;border-radius:0.25rem;padding:18px;box-shadow:0 2px 8px rgba(0,0,0,.04);">
                                <div class="d-flex align-items-center justify-content-between flex-wrap">
                                    <div class="d-flex align-items-center mb-2 mb-md-0">
                                        <h5 class="mb-0 me-4" style="color:var(--theme-color-primary, #119A48);">Search Results</h5>
                                        <span class="fw-bold" style="color:#1e293b;font-size:1rem;">
                                            {{ $results_count ?? ($publications->total() ?? 0) }} {{ ($results_count ?? ($publications->total() ?? 0)) == 1 ? 'result' : 'results' }} found
                                        </span>
                                    </div>
                                    @if(isset($search_time))
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-clock me-2" style="color:#64748b;font-size:0.9rem;"></i>
                                        <span style="color:#64748b;font-size:0.9rem;">
                                            {{ $search_time }} ms
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                            
                            @if(isset($_GET['tag']) && !empty($_GET['tag']))
                                @php
                                    $tagId = $_GET['tag'];
                                    $tag = \App\Models\Tag::find($tagId);
                                    $tagName = $tag ? $tag->tag_text : 'Tag #' . $tagId;
                                    $auGold = settings()->au_gold ?? '#B4A269';
                                    $auPlum = settings()->au_plum ?? '#522B39'; // Agenda 2063 Plum (PANTONE 3415 C)
                                @endphp
                                <div class="mb-3" style="background-color: {{ $auGold }}; border: 1px solid {{ $auGold }}; border-radius: 0.25rem; padding: 0.75rem 1rem;">
                                    <div style="color: {{ $auPlum }};">
                                        <strong>Tag:</strong> {{ $tagName }}
                                    </div>
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
                        .sidebar-content{background:#fff;border:1px solid #e2e8f0;border-radius:0.25rem;padding:18px;box-shadow:0 2px 8px rgba(0,0,0,.04);margin-bottom:20px}
                        .sidebar-content h5.popular-tags-title{margin-bottom:10px;font-size:15px;font-weight:500;text-transform:capitalize;color:#2d3748}
                        .sidebar-content .btn-outline-primary{color:var(--theme-color-primary, #119A48);border-color:var(--theme-color-primary, #119A48)}
                        .sidebar-content .btn-outline-primary:hover{background:var(--theme-color-primary, #119A48);color:#fff}
                        .sidebar-tags{display:flex;flex-wrap:wrap;gap:0.5rem}
                        .sidebar-tag-pill{display:inline-block;padding:0.3rem 0.7rem;font-size:0.8rem;font-weight:500;color:#ffffff !important;text-decoration:none;border-radius:0.25rem;transition:all 0.2s ease;white-space:nowrap;background-color:var(--theme-color-primary, #119A48) !important;border:1px solid rgba(17,154,72,0.3)}
                        .sidebar-tag-pill:hover{transform:translateY(-2px);box-shadow:0 2px 6px rgba(17,154,72,0.3);color:#ffffff !important;text-decoration:none;background-color:var(--theme-color-primary, #119A48) !important}
                    </style>

                    {{-- Popular Tags --}}
                    @if(isset($tags) && count($tags) > 0)
                    <div class="sidebar-content">
                      <h5 class="popular-tags-title">Popular Tags</h5>
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
                    @else
                    {{-- Fallback: Show empty state or get tags from view composer --}}
                    @php
                        $allTags = \App\Models\Tag::orderBy('id', 'desc')->limit(10)->get();
                    @endphp
                    @if($allTags->count() > 0)
                    <div class="sidebar-content">
                      <h5 class="popular-tags-title">Popular Tags</h5>
                      <div class="sidebar-tags">
                        @foreach($allTags as $tag)
                        <a href="{{ url('records')}}?tag={{$tag->id}}" 
                           class="sidebar-tag-pill" 
                           title="{{$tag->tag_text}}">
                          {{ truncate($tag->tag_text,15) }}
                        </a>
                        @endforeach
                      </div>
                    </div>
                    @endif
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
                        <p class="mb-1" style="font-size:0.8rem;color:#64748b;">{{ Str::limit(strip_tags(clean_unicode(publication_description_for_list($pub->description ?? ''))), 100) }}</p>
                        <div class="d-flex align-items-center" style="font-size:0.75rem;color:#94a3b8;">
                          @if($pub->author)
                          <span class="mr-2"><i class="fa fa-user mr-1"></i>
                            @if(!empty($pub->author->orcid))
                                <a href="https://orcid.org/{{ $pub->author->orcid }}" target="_blank" rel="noopener noreferrer" title="View {{ $pub->author->name }}'s ORCID profile" style="color: inherit; text-decoration: none;">
                                    {{ $pub->author->name }}
                                    <i class="fa fa-external-link-alt" style="font-size: 0.65rem; margin-left: 2px;"></i>
                                </a>
                            @else
                                {{ $pub->author->name }}
                            @endif
                          </span>
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
                        <p class="mb-1" style="font-size:0.8rem;color:#64748b;">{{ Str::limit(strip_tags(clean_unicode(publication_description_for_list($pub->description ?? ''))), 100) }}</p>
                        <div class="d-flex align-items-center" style="font-size:0.75rem;color:#94a3b8;">
                          @if($pub->author)
                          <span class="mr-2"><i class="fa fa-user mr-1"></i>
                            @if(!empty($pub->author->orcid))
                                <a href="https://orcid.org/{{ $pub->author->orcid }}" target="_blank" rel="noopener noreferrer" title="View {{ $pub->author->name }}'s ORCID profile" style="color: inherit; text-decoration: none;">
                                    {{ $pub->author->name }}
                                    <i class="fa fa-external-link-alt" style="font-size: 0.65rem; margin-left: 2px;"></i>
                                </a>
                            @else
                                {{ $pub->author->name }}
                            @endif
                          </span>
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
        </div>
    @endsection

    @section('scripts')
        @include('common.select2')
    @endsection
