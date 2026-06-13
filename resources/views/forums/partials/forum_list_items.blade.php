@php
    $my_forums = $my_forums ?? [];
@endphp

@foreach ($forums as $forum)
    @include('forums.partials.forum_card', ['forum' => $forum, 'my_forums' => $my_forums])
@endforeach
