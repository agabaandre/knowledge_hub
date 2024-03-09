<div class="map-section-container">
    <div class="heading map-heading"> <h2>Member States</h2></div>
    
    <div class="mapcontainer">  
      <div class="map">
      <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
          viewBox="0 0 263.201 289.81" style="enable-background:new 0 0 263.201 289.81;"
        xml:space="preserve">
        
      @foreach($countries as $country)
       @if($country->svg_path)
            <path class="st0 our-member" 
            id="{{$country->id}}"  
            data-info="<div>{{$country->name}}</div>"
            d="{{$country->svg_path}}">Member</path>
        @endif
      @endforeach

      
        <g>
        <path class="st0 our-member" d="M -19.16 78.399 C -19.16 79.418 -18.327 80.285 -17.738 79.057 C -17.452 78.462 -18.844 77.819 -18.844 79.551"></path>
        <path class="st0 our-member" d="M -20.2 85.213 C -20.2 86.981 -20.373 88.901 -19.082 87.296 C -18.865 87.027 -19.02 84.443 -19.641 85.213 C -20.108 85.794 -19.041 87.528 -20.013 87.528"></path>
        </g>


      </svg>

      </div>

      <div class="detail" >
        <div class="mapcontent" >
          
        </div>
      </div>
    </div>

</div>