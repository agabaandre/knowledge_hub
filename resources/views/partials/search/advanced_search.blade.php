<div class="row py-3 advanced_filters">

<a data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample" class="advanced col-lg-12" style="color:{{ $text_color ?? '#bba66d'}}; font-size:14px;">
Advance your Search With Filters <i class="fa fa-angle-down"></i>
</a>

<div class="col-lg-12 collapse" id="collapseExample">
<div class="card card-body">
    
    <div class="row">

    <div class="col-md-4">
        <label class="text-bold"><small>RCC</small></label>
        @include('partials.regions.dropdown',['class'=>'class'])
    </div>

    <div class="col-md-4">
       <label class="text-bold"><small>Country</small></label>
       @include('partials.countries.dropdown',['class'=>'class'])
    </div>

    <div class="col-md-4">
       <label class="text-bold"><small>Source</small></label>
       @include('partials.authors.dropdown',['class'=>'class'])
    </div>

    <div class="col-md-4">
       <label class="text-bold"><small>Thematic Area</small></label>
       @include('partials.publications.theme_dropdown',['class'=>'class'])
    </div>

    <div class="col-md-4">
       <label class="text-bold"><small>Sub-Thematic Area</small></label>
       @include('partials.publications.subtheme_dropdown',['class'=>'class'])
    </div>

    <div class="col-md-4">
       <label class="text-bold"><small>Type</small></label>
       @include('partials.publications.filetype_dropdown',['class'=>'class'])
    </div>


    </div>

</div>
</div>

</div>