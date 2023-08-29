@extends('layouts.app')

@section('styles')

@endsection

@section('content')

<div class="gray pt-2">
<div class="container">
<div class="row">
			
	<!-- Item Wrap Start -->
	<div class="col-lg-12 col-md-12 col-sm-12 ">
		
		<!-- All jobs -->
		<div class="row">

          @if($record->is_embedded)

            <div class="col-lg-12 text-center pt-2 pb-2">
                <h2 class="post-title text-center">{!! $record->title !!}</h2>
                <h5>Categorization: {{ $record->sub_category->category->category_name }}</h5>
                <p>{!! $record->description !!}</p>
            </div>

            <iframe class="col-md-12" src="{{$record->url}}" style="width: 100%; height: 55rem; border: 0; background: #eaf8ff" allowfullscreen="true"></iframe>

            @else

            <div class="article_detail_wrapss single_article_wrap format-standard">
                            <div class="article_body_wrap">
                                <h2 class="post-title">{!! $record->title !!}</h2>
                                <h6 class="text-muted"><i class="fa fa-map-pin"></i> {{$record->country->name}}</h6>
                                <h5> {{ $record->sub_category->category->category_name }}</h5>
                                 
                               
                                <p>{!! $record->description !!}</p>

                                 @if($record->url)
                                 <a href="{{ $record->url }}"  target="_blank" class="btn btn-theme">Browse</a>
                                 @endif

                            </div>
                </div>
                        
             @endif

       
		</div>
		

	</div>
			
</div>
</div>
</div>

@endsection