<!-- row -->
<div class="row align-items-center justify-content-center">
            
            @foreach($adminunits as $unit)
    
                <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-6" data-aos="flip-left">
                    <div class="cats-wrap text-center" style="box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;">
                        <a href="{{ url('adminunits/details')}}?id={{$unit->id}}" class="cats-box d-block rounded bg-white px-2 py-4">
                            <div class="text-center mb-2 mx-auto position-relative d-inline-flex align-items-center justify-content-center p-3 circle" style="background-image:url({{ storage_link('uploads/adminunits/'.@$unit->logo) }}); background-position:center; background-size:cover;">
                            </div>
                            <div class="cats-box-caption">
                                <h5 class="fs-md mb-0 ft-medium m-catrio">{{truncate($unit->name,50)}}</h5>
                            </div>
                        </a>
                    </div>
                </div>
    
            @endforeach
                   
</div>