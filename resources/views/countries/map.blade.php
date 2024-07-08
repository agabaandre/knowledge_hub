<div class="map-section-container">
    <div class="heading map-heading"> <h2>Member States</h2></div>
    
    <div class="row">  
      <div class="col-lg-8 map">

  
      <svg version="1.1"  xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
    
        <g id="admin0"> 
          @foreach($countries as $country)
          @if($country->svg_path)
                <path class="st0 our-member"
                title="{{$country->name}}" 
                id="{{$country->id}}"  
                data-info='<div class="card mb-3" style="max-width: 100%;">
                          <div class="row g-0">
                            <div class="col-md-4 px-0">
                              <img src="{{ asset('assets/img/flags/'.@$country->flag) }}" class="img-fluid rounded-start" alt="{{$country->name}}">
                            </div>
                            <div class="col-md-8">
                              <div class="card-body">
                                <h5 class="card-title">{{$country->name}}, {{$country->region->region_name}}</h5>
                                <p class="card-text">{{$country->resources}} Resources Published, <a class="btn btn-sm btn-dark text-white" href="{{ url('countries/details')}}?state={{$country->id}}">View Resources</a></p>
                              </div>
                            </div>
                          </div>
                        </div>'
                d="{{$country->svg_path}}">
                <desc id="desc-{{$country->id}}">{{$country->name}}</desc>
              </path>
            @endif
          @endforeach
        </g>
      
      </svg>

      </div>

      <div class="col-lg-4" >
          <div class="mappopover" id="mappopover"></div>
      
      </div>
    </div>

</div>