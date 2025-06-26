<div class="slide_items col-lg-8 col-md-12 col-sm-12">
    @foreach($subthemes as $subtheme)
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 item" >
            <div class="cats-wrap text-center spot-item mt-1" style="z-index: 1000!important;">
                <a href="{{ url('records/subtheme')}}?subtheme={{$subtheme->id}}" class="cats-box d-block rounded bg-white px-2 py-4">
                    <div class="text-center mb-2 mx-auto position-relative d-inline-flex align-items-center justify-content-center p-3 py-3 " >
                        <img src="{{ asset('frontend/img/icons/'.$subtheme->icon)}}" style="max-width: 60%;"/>
                    </div>
                    <div class="cats-box-caption">
                        <h4 class="fs-sm mb-0 ft-sm m-catrio" data-bs-toggle="tool-tip" data-bs-title="{{$subtheme->description}}">{{truncate($subtheme->description,16)}}</h4>
                    </div>
                </a>
            </div>
        </div>
    @endforeach
</div>