@php
    $listOffset = (int) ($listOffset ?? 0);
    $i = $listOffset;
@endphp

@foreach ($publications as $row)
    @php
        $i++;
    @endphp
    @include('partials.publications.publication_feed_card', ['row' => $row, 'i' => $i])
@endforeach
