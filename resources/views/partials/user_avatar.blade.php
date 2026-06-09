@php
    $photo = $user->photo ?? null;
    $size = $size ?? '25px';
    $class = $class ?? '';
@endphp

<span class="user-avatar-wrapper {{ $class }}" style="display: inline-flex; align-items: center; justify-content: center; width: {{ $size }}; height: {{ $size }}; min-width: {{ $size }}; min-height: {{ $size }}; max-width: {{ $size }}; max-height: {{ $size }}; border-radius: 50%; overflow: hidden; background-color: #e2e8f0; border: 1px solid #cbd5e0; flex-shrink: 0;">
    @if(!empty($photo) && $photo !== asset('assets/images/user.jpg'))
        <img src="{{ $photo }}" 
             alt="{{ $user->name ?? 'User' }}" 
             class="user-avatar-img"
             style="width: 100%; height: 100%; object-fit: cover; display: block;"
             onerror="this.style.display='none'; this.parentElement.querySelector('.user-avatar-icon').style.display='flex';">
        <span class="user-avatar-icon" style="display: none; width: 100%; height: 100%; align-items: center; justify-content: center; color: #718096; font-size: calc({{ $size }} * 0.5);">
            <i class="fa fa-user"></i>
        </span>
    @else
        <span class="user-avatar-icon" style="display: flex; width: 100%; height: 100%; align-items: center; justify-content: center; color: #718096; font-size: calc({{ $size }} * 0.5);">
            <i class="fa fa-user"></i>
        </span>
    @endif
</span>

