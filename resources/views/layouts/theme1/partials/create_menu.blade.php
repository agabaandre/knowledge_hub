@php
    $createForumUrl = auth()->check()
        ? route('forums.create')
        : route('login', ['redirect' => route('forums.create'), 'reason' => 'forum']);
    $createPublicationUrl = auth()->check()
        ? route('account.publish')
        : route('login', ['redirect' => route('account.publish'), 'reason' => 'publication']);
@endphp

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ request()->routeIs('account.publish') || request()->routeIs('account.publication') || request()->routeIs('forums.create') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
        Create
    </a>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="{{ $createForumUrl }}">Forum Discussion</a></li>
        <li><a class="dropdown-item" href="{{ $createPublicationUrl }}">Publish Resource</a></li>
    </ul>
</li>
