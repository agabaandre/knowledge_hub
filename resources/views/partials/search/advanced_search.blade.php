<div class="row py-3 advanced_filters">

@php

$advanced_filter = (@$search->rcc || @$search->country_id || @$search->author_id || @$search->file_type_id || @$search->thematic_area_id)?true:false;

@endphp

<a data-toggle="collapse" href="#collapseExample" role="button" 
aria-expanded="{{ $advanced_filter }}" aria-controls="collapseExample" class="advanced col-lg-12" style="color:{{ $text_color ?? '#bba66d'}}; font-size:14px;">
Advance your Search With Filters <i class="fa fa-angle-down"></i>
</a>

<div class="col-lg-12 {{ ($advanced_filter)?'':'collapse' }}" id="collapseExample">
<div class="card card-body">
    
    <div class="row">

    <div class="col-md-4">
        <label class="text-bold"><small>RCC</small></label>
        @include('partials.regions.dropdown',['class'=>'rcc select2','selected'=>@$search->rcc,'allfield'=>'All'])
    </div>

    <div class="col-md-4">
       <label class="text-bold"><small>Country</small></label>
       @include('partials.countries.dropdown',['class'=>'country select2','selected'=>@$search->country_id,'allfield'=>'All'])
    </div>

    <div class="col-md-4">
       <label class="text-bold"><small>Source</small></label>
       @include('partials.authors.dropdown',['class'=>'author select2','selected'=>@$search->author_id,'allfield'=>'All'])
    </div>

    <div class="col-md-4">
       <label class="text-bold"><small>Thematic Area</small></label>
       @include('partials.publications.theme_dropdown',['class'=>'theme select2','selected'=>@$search->thematic_area_id,'allfield'=>'All'])
    </div>

    <div class="col-md-4">
       <label class="text-bold"><small>Sub-Thematic Area</small></label>
       @include('partials.publications.subtheme_dropdown',['class'=>'subtheme select2','selected'=>@$search->sub_thematic_area_id,'allfield'=>'All'])
    </div>

    <div class="col-md-4">
       <label class="text-bold"><small>Type</small></label>
       @include('partials.publications.filetype_dropdown',['class'=>'filetype select2','selected'=>@$search->file_type_id,'allfield'=>'All'])
    </div>
    </div>

</div>
</div>

</div>

<script>

$('.rcc').on('change', function(e){
  

   if($(this).val()){
      var regions = JSON.parse('<?php echo json_encode($regions->toArray()); ?>');
      let  selectedRegion = regions.find(item=>item.id === parseInt($(this).val()));

      $('.country').html('<option value="" selected >All</option>');

      selectedRegion.countries.forEach(function(item){

         const option = `<option value="${item.id}">${item.name}</option>`;
         $('.country').append(option);

      });

   }

});


$('.theme').on('change', function(e){
  
   if($(this).val()){

    var themes = JSON.parse('<?php echo json_encode($themes->toArray()); ?>');
  

   let  selectedTheme = themes.find(item=>item.id === parseInt($(this).val()));

   $('.subtheme').html('<option value="" selected >All</option>');

   selectedTheme.subthemes.forEach(function(item){

      const option = `<option value="${item.id}">${item.description}</option>`;
      $('.subtheme').append(option);

   });

   }

});


</script>