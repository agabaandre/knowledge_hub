@if(count($health_emergencies) == 0)
    <ul class="nav-dropdown nav-submenu" style="right: auto; display: none;">
        @foreach($tags as $tag)
            @if($tag->is_health_emergency)
                <li><a href="{{ tag_records_url($tag) }}">{{ $tag->tag_text }}</a></li>
            @endif
        @endforeach
    </ul>
@else
<div class="mega-menu" id="health-emergencies-mega-menu" role="region" aria-label="{{ __('frontend_nav.health_emergencies') }}">
    <div class="mega-menu-container">
        <div class="mega-menu-grid">
            <aside class="mega-menu-sidebar">
                <p class="mega-sidebar-label">{{ __('frontend_nav.health_emergencies') }}</p>
                <ul>
                    @foreach($filteredTags as $tag)
                        <li
                            data-tag-id="{{ $tag->id }}"
                            class="mega-sidebar-item {{ $loop->first ? 'active' : '' }}"
                        >
                            <button
                                type="button"
                                class="mega-sidebar-btn"
                                data-tag-id="{{ $tag->id }}"
                                aria-controls="mega-panel-{{ $tag->id }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                            >
                                {{ $tag->tag_text }}
                            </button>
                            <a href="{{ tag_records_url($tag) }}" class="mega-sidebar-link" title="{{ __('frontend_nav.view_all_resources') }}">
                                <i class="fa fa-external-link-alt" aria-hidden="true"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </aside>

            <div class="mega-menu-content">
                @foreach($filteredTags as $tag)
                    @php
                        $publications = get_tag_ublications($tag);
                    @endphp
                    <div
                        id="mega-panel-{{ $tag->id }}"
                        class="mega-grid-content {{ $loop->first ? 'active' : '' }}"
                        data-tag-id="{{ $tag->id }}"
                        role="tabpanel"
                        @if(!$loop->first) hidden @endif
                    >
                        <div class="mega-panel-header">
                            <div>
                                <h4 class="mega-panel-title">{{ $tag->tag_text }}</h4>
                                <p class="mega-panel-subtitle">{{ __('frontend_nav.featured_resources') }}</p>
                            </div>
                            <a href="{{ tag_records_url($tag) }}" class="mega-view-all">
                                {{ __('frontend_nav.view_all_resources') }}
                                <i class="fa fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>

                        @if($publications->isEmpty())
                            <div class="mega-empty">
                                <p>{{ __('frontend_nav.no_resources_for_topic') }}</p>
                                <a href="{{ tag_records_url($tag) }}" class="mega-view-all">{{ __('frontend_nav.browse_topic') }}</a>
                            </div>
                        @else
                            <div class="mega-cards-grid">
                                @foreach($publications as $pub)
                                    <a class="mega-card" href="{{ publication_url($pub) }}">
                                        <div class="mega-card-image">
                                            <img src="{{ $pub->cover }}" alt="" loading="lazy">
                                        </div>
                                        <div class="mega-card-body">
                                            <h3 class="mega-card-title">{{ truncate($pub->title, 90) }}</h3>
                                            <div class="mega-card-meta">
                                                @if($pub->theme->description ?? null)
                                                    <span class="mega-card-theme">{{ truncate($pub->theme->description, 40) }}</span>
                                                @endif
                                                <span class="mega-card-date">{{ text_date($pub->created_at) }}</span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const megaRoot = document.getElementById('health-emergencies-mega-menu');
    if (!megaRoot) return;

    const sidebarItems = megaRoot.querySelectorAll('.mega-sidebar-item');
    const sidebarButtons = megaRoot.querySelectorAll('.mega-sidebar-btn');
    const panels = megaRoot.querySelectorAll('.mega-grid-content');

    function activatePanel(tagId) {
        sidebarItems.forEach(function (item) {
            const isActive = item.dataset.tagId === tagId;
            item.classList.toggle('active', isActive);
            const btn = item.querySelector('.mega-sidebar-btn');
            if (btn) btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        panels.forEach(function (panel) {
            const isActive = panel.dataset.tagId === tagId;
            panel.classList.toggle('active', isActive);
            if (isActive) {
                panel.removeAttribute('hidden');
            } else {
                panel.setAttribute('hidden', 'hidden');
            }
        });
    }

    sidebarButtons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            activatePanel(this.dataset.tagId);
        });
        btn.addEventListener('mouseenter', function () {
            activatePanel(this.dataset.tagId);
        });
    });
});
</script>
@endif
