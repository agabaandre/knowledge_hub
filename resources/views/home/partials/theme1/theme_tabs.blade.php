<div class="row justify-content-center custom-row categories" id="themes">
    @foreach ($themes as $theme)
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-6 item custom-row-item mb-4">
            <div class="cats-wrap text-center spot-item mt-1" style="z-index: 1000!important;">
                <a href="{{ url('records/subtheme') }}?subtheme={{ $theme->id }}"
                    class="cats-box d-block rounded shadow-sm bg-white px-3 py-3"
                    style="transition: transform 0.3s, box-shadow 0.3s;">
                    <div
                        class="text-center mb-2 mx-auto position-relative d-inline-flex align-items-center justify-content-center p-1 py-2">
                        <i class="fa {{ $theme->icon }} fa-3x icon-color" style="color: #2980b9;"></i>
                    </div>
                    <div class="cats-box-caption">
                        <h4 class="fs-sm mb-0 ft-sm m-catrio text-color-primary" data-bs-toggle="tooltip"
                            data-bs-title="{{ truncate($theme->description, 410) }}" style="color: #34495e;">
                            {{ truncate($theme->description, 17) }}
                        </h4>
                    </div>
                </a>
            </div>
        </div>
    @endforeach
</div>
