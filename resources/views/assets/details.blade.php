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


            <div class="article_detail_wrapss single_article_wrap format-standard">
                            <div class="article_body_wrap">
                                <h2 class="post-title">{!! $asset->asset_name !!}</h2>
                                <h5 class="text-muted"><i class="fa fa-map-pin"></i> {{$asset->country->name}}</h5>

                                 <p>{!! $asset->asset_desc !!}</p>
                                 <a href="{{ $asset->url }}"  target="_blank" class="btn btn-theme">Browse</a>
                            </div>
                        </div>
       
		</div>
		

	</div>
			
</div>
</div>
</div>

@endsection