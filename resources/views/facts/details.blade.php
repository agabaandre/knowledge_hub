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
                                <h2 class="post-title">{!! $fact->fact_title !!}</h2>
                                <h5 class="text-muted mb-3 mt-2"> 
                                    <blockquote>{!! $fact->fact_summary !!}</blockquote>
                                </h5>

                                 <p>{!! $fact->fact_description !!}</p>
                            </div>
                        </div>
       
		</div>
		

	</div>
			
</div>
</div>
</div>

@endsection