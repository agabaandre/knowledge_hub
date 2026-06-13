@foreach ($events as $ev)
    @php
        $cover = $ev->banner_image ? asset($ev->banner_image) : asset('assets/images/cover.png');
        $start = $ev->startdate ? \Carbon\Carbon::parse($ev->startdate)->format('M d, Y') : '';
        $end = $ev->enddate ? \Carbon\Carbon::parse($ev->enddate)->format('M d, Y') : '';
        $range = trim($start . ($end ? ' - ' . $end : ''));
        $isPast = $ev->enddate
            ? \Carbon\Carbon::parse($ev->enddate)->isPast()
            : ($ev->startdate ? \Carbon\Carbon::parse($ev->startdate)->isPast() : false);
        $detailsUrl = url('events/'.$ev->id);
    @endphp
    <div class="event-card event-slide" style="cursor:pointer;" @if($detailsUrl) onclick="window.open('{{ $detailsUrl }}','_blank')" @endif>
        <div class="event-cover"><img src="{{ $cover }}" alt="{{ $ev->title }}" onerror="this.onerror=null;this.src='{{ asset('assets/images/cover.png') }}'"/></div>
        <div class="event-body">
            <div class="event-title" style="overflow-wrap: break-word; word-wrap: break-word;">{{ Str::limit(strip_tags($ev->title),70) }}</div>
            <div class="event-meta"><i class="fa fa-clock-o mr-1"></i>{{ $range ?: 'Date TBA' }}</div>
            @if ($ev->venue)
                <div class="event-meta"><i class="fa fa-map-marker mr-1"></i>{{ $ev->venue }}</div>
            @endif
            @if (!empty($ev->organized_by))
                <div class="event-meta"><i class="fa fa-building mr-1"></i>{{ $ev->organized_by }}</div>
            @endif
            @if (!empty($ev->description))
                <div class="event-desc">{!! Str::words(strip_tags($ev->description), 30, '...') !!}</div>
            @endif
            <div class="event-actions">
                @if (!$isPast && $ev->registration_link)
                    <a href="{{ $ev->registration_link }}" target="_blank" class="btn btn-sm btn-success" onclick="event.stopPropagation();"><i class="fa fa-edit mr-1"></i>Register</a>
                @endif
            </div>
        </div>
    </div>
@endforeach
