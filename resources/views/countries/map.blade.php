<div class="map-section-container">
    <div class="heading map-heading"> <h2>Member States</h2></div>
    
    <div class="row">  
      <div class="col-lg-12 map">

      <svg version="1.1"  xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
    
        <g id="admin0" > 
          @foreach($countries as $country)
        @if($country->svg_path)
        <path class="st0 our-member"
        title="{{$country->name}}" 
        id="{{$country->id}}"
        onmouseleave="$('.popover').hide();"  
        data-info='<div class="row g-0">
          <div class="col-md-4 px-0">
            <img width="100px" src="{{ asset('assets/img/flags/' . @$country->flag) }}" class="img-fluid rounded-start" alt="{{$country->name}}">
          </div>
          <div class="col-md-8">
            <div class="card-body">
            <h5 class="card-title">{{$country->name}}, {{$country->region->region_name}}</h5>
            <p class="card-text">{{$country->resources}} Resources Published, <a class="btn btn-sm btn-dark text-white" href="{{ url('countries/details')}}?state={{$country->id}}">View Resources</a></p>
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
    </div>

</div>

@section('scripts')
 <script  src="{{ asset('assets/js/map.js')}}"></script>
@endsection
