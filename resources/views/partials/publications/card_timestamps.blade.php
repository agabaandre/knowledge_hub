@php
    $pub = $row ?? $publication ?? null;
@endphp
@if($pub)
<span class="mr-2"><i class="lni lni-calendar mr-1"></i>Last updated: {{ publication_content_updated_ago($pub) }}</span>
@if(publication_last_visited_at($pub))
<span class="mr-2"><i class="fa fa-history mr-1"></i>Last visit: {{ time_ago(publication_last_visited_at($pub)) }}</span>
@endif
@endif
