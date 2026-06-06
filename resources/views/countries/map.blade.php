<div class="map-section-container">
    <div class="map-wrapper">
      <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1000 650" preserveAspectRatio="xMidYMid meet" style="overflow将至 visible;">
        <g id="admin0"> 
          @foreach($countries as $country)
            @if($country->svg_path)
              <path class="st0 our-member"
                    title="{{$country->name}} - {{$country->resources}} Resources" 
                    id="{{$country->id}}"
                    data-country-id="{{$country->id}}"
                    data-country-name="{{ strtolower($country->name) }}"
                    onclick="window.location.href='{{ country_detail_url($country) }}'" 
                    data-info='<div class="map-popover-content">
                        <div class="d-flex align-items-center mb-2">
                          <img width="60px" src="{{ asset('assets/img/flags/' . ($country->flag ?? '')) }}" class="rounded me-2" alt="{{$country->name}}">
                          <div>
                            <h6 class="mb-0 fw-bold">{{$country->name}}</h6>
                            <small class="text-muted">{{$country->region->region_name ?? 'N/A'}}</small>
                          </div>
                        </div>
                        <div class="mt-2">
                          <span class="badge bg-primary">{{$country->resources}} Resources</span>
                        </div>
                        <small class="text-muted d-block mt-2"><i class="fa fa-mouse-pointer"></i> Click for details</small>
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

<style>
.map-wrapper {
    position: relative;
    width: 100%;
    background: linear-gradient(135deg, #f8fafc 0%, #e8ecf1 100%);
    border-radius: 12px;
    padding: 1.5rem;
    overflow: visible;
    border: 2px solid #e2e8f0;
}

.map-wrapper svg {
    width: 100% !important;
    height: auto !important;
    min-height: 600px !important;
    max-height: 700px !important;
    display: block !important;
    padding: 1rem !important;
    padding-bottom: 3rem !important;
    float: none !important;
    background: transparent;
    overflow: visible !important;
}

.map-popover-content {
    padding: 0.5rem;
}

.map-popover-content img {
    border: 2px solid var(--theme-color-primary, #119A48);
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.popover {
    max-width: 280px;
    border: 2px solid var(--theme-color-primary, #119A48);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    border-radius: 12px;
    z-index: 1000;
}

.popover-header {
    background: linear-gradient(135deg, var(--theme-color-primary, #119A48) 0%, var(--theme-color-secondary, #0d7a3a) 100%);
    color: white;
    border: none;
    border-radius: 10px 10px 0 0;
    padding: 0.75rem 1rem;
    font-weight: 600;
}

.popover-body {
    padding: 1rem;
    background: white;
}

.popover-body .badge {
    background-color: var(--theme-color-primary, #119A48) !important;
    color: white;
}

/* Ensure map paths are visible and use theme colors */
#admin0 path.st0.our-member {
    fill: var(--theme-color-primary, #119A48) !important;
    stroke: rgba(255, 255, 255, 0.8) !important;
    stroke-width: 1.5px !important;
    vector-effect: non-scaling-stroke;
    pointer-events: all;
}
</style>
