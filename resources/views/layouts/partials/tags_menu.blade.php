
@if(count($health_emergencies)==0)
    <ul class="nav-dropdown nav-submenu" style="right: auto; display: none;">
     @foreach($tags as $tag)
         @if($tag->is_health_emergency)
          <li><a href="{{ url('records')}}?tag={{$tag->id}}">{{$tag->tag_text}}</a></li>
         @endif
        @endforeach
    </ul>
@else

<style type="text/css">
 .mega-menu-sidebar li.active > a {
  font-weight: bold;
  /* or any highlight style */
}

.mega-grid-content {
  display: none;
}

.mega-grid-content.active {
  display: block;
}


</style>

<div class="mega-menu">
  <div class="mega-menu-container">
    <div class="mega-menu-grid">

      {{-- Sidebar --}}
      <div class="mega-menu-sidebar">
        <ul>
          @foreach($tags as $tag)
            @if($tag->is_health_emergency)
              <li 
                data-tag-id="{{ $tag->id }}" 
                class="{{ $loop->first ? 'active' : '' }}"
              >
                <a href="{{ url('records') }}?tag={{ $tag->id }}">
                  {{ $tag->tag_text }}
                </a>
              </li>
            @endif
          @endforeach
        </ul>
      </div>

      {{-- Content panels --}}
      <div class="mega-menu-content">
        @foreach($tags as $tag)
          @if($tag->is_health_emergency)
            <div 
              class="mega-grid-content {{ $loop->first ? 'active' : '' }}" 
              data-tag-id="{{ $tag->id }}" 
              style="display: {{ $loop->first ? 'block' : 'none' }};"
            >
              @php 

              $count = 0; 

              @endphp
              @foreach( get_tag_ublications($tag) as $pub)
                @if($count < 6)
                  <a class="mega-item" href="{{ url('records/resource') }}?id={{ $pub->id }}">
                    <div class="mega-image">
                      <img src="{{ $pub->cover }}">
                    </div>
                    <div class="mega-info">
                      <h3 class="mega-title">{{ truncate($pub->title, 125) }}</h3>
                      <div class="mega-meta">
                        <span class="mega-date text-bold">{{ $pub->theme->description ?? '' }}</span>
                        <span class="mega-date">{{ text_date($pub->created_at) }}</span>
                      </div>
                    </div>
                  </a>
                @endif
                @php $count++; @endphp
              @endforeach
            </div>
          @endif
        @endforeach
      </div>

    </div>
  </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
  const sidebarItems = document.querySelectorAll('.mega-menu-sidebar li');
  const contentSections = document.querySelectorAll('.mega-grid-content');

  sidebarItems.forEach(item => {
    item.addEventListener('mouseenter', function () {
      const tagId = this.dataset.tagId;

      // remove active on all
      sidebarItems.forEach(i => i.classList.remove('active'));
      contentSections.forEach(sec => {
        sec.classList.remove('active');
        sec.style.display = 'none';
      });

      // set this one active
      this.classList.add('active');
      const target = document.querySelector(`.mega-grid-content[data-tag-id="${tagId}"]`);
      if (target) {
        target.classList.add('active');
        target.style.display = 'block';
      }
    });
  });
});

</script>


@endif