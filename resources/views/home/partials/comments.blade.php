<!-- Data for popover header -->
<div class="pop-head d-none">
<h6 class="text text-success">
Comments
</h6>
</div>
			
<!-- Data for popover content -->
<div class="pop-body d-none" style="max-height:50%;" onmouseout="$().tooltip('hide')" onblur="">
    <div class="text"> 
    @if(count($row->comments)>0)
    <div class="row px-3" id="comments{{ $i}}">
        <ul class="list-group col-lg-12">
            @php
             $com_count=0;
            @endphp

            @foreach($row->comments as $comm) 

            @php
            $com_count++;
            @endphp
           
            <li class="app-comment">
                <h6><small>{{@$comm->user->name}}</small></h6>
                <p>{{$comm->comment}}</p>
                <small class="text-success">{{time_ago($comm->created_at)}}</small>
            </li>
            @endforeach
            <a class="py-3" href="{{url('records/show')}}?id={{$row->id}}">View All</a>
        </ul>
    </div>
     @endif
    </div>
</div>