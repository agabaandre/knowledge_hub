@extends('layouts.app')

@section('styles')
@endsection

@section('content')
    <div class="gray py-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
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

                {{-- <div class="col-lg-4">
                    @include('publications.partials.facts')
                    <br />
                </div> --}}
            </div>
        </div>
    @endsection

    @section('scripts')
        @include('common.select2')
    @endsection
