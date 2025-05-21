
@if(count($health_emergencies)==0)
    <ul class="nav-dropdown nav-submenu" style="right: auto; display: none;">
     @foreach($tags as $tag)
         @if($tag->is_health_emergency)
          <li><a href="{{ url('records')}}?tag={{$tag->id}}">{{$tag->tag_text}}</a></li>
         @endif
        @endforeach
    </ul>
@else

<div class="mega-menu">
                            <div class="mega-menu-container">
                                <div class="mega-menu-grid">
                                    <!-- Category filters on the left -->
                                    <div class="mega-menu-sidebar">
                                        <ul>
                                            @foreach($tags as $tag)
                                             @if($tag->is_health_emergency)
                                              <li><a href="{{ url('records')}}?tag={{$tag->id}}">{{$tag->tag_text}}</a></li>
                                             @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                    
                                    <!-- mega posts grid -->
                                    <div class="mega-menu-content">
                                        <div class="mega-grid">
                                            @php
                                              $count =0;
                                            @endphp



                                            @foreach($health_emergencies as $pub)
                                            @if($count<6)
                                            <a class="mega-item" href="{{ url('records/resource')}}?id={{$pub->id}}">
                                                <div class="mega-image">
                                                    <img src="{{$pub->cover}}">
                                                </div>
                                                <div class="mega-info">
                                                    <h3 class="mega-title">{{truncate($pub->title,125)}}</h3>
                                                    <div class="mega-meta">

                                                        <span class="mega-date text-bold">{{$pub->theme->description ?? ''}}</span>
                                                        <span class="mega-date">{{ text_date($pub->created_at)}}</span>
                                                    </div>
                                                </div>
                                            </a>
                                            @endif
                                             @php $count ++; @endphp
                                            @endforeach
                                            
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

@endif