
        <h4 class="py-3">Did you know about these facts?</h4>
        
        @foreach($facts as $row)
        <div class="card mb-1" style="width: 100%;" data-aos="slide-left">
        <div class="card-body">
            <h5 class="card-title">{{$row->fact_title}}</h5>
            <h6 class="card-subtitle mb-2 text-muted">{{$row->fact_summary}}</h6>
            <a href="{{ url('facts/fact') }}?ref={{$row->id}}" class="card-link text-primary">Learn More</a>
        </div>
        </div>
        @endforeach

  