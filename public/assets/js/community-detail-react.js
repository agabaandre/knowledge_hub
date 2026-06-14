(function () {
    'use strict';

    var cfg = window.communityDetailReactConfig;
    if (!cfg || typeof React === 'undefined' || typeof ReactDOM === 'undefined') {
        return;
    }

    var useState = React.useState;
    var useEffect = React.useEffect;
    var useCallback = React.useCallback;
    var primaryColor = cfg.primaryColor || '#119A48';

    function applyPaneVisibility(activeTab) {
        var paneIds = cfg.paneIds || {};
        Object.keys(paneIds).forEach(function (tabId) {
            var el = document.getElementById(paneIds[tabId]);
            if (!el) {
                return;
            }
            var isActive = tabId === activeTab;
            el.classList.toggle('show', isActive);
            el.classList.toggle('active', isActive);
            el.style.display = isActive ? 'block' : 'none';
        });
    }

    function tabFromLocation() {
        var params = new URLSearchParams(window.location.search);
        var tab = params.get('tab');
        if (tab && (cfg.tabs || []).some(function (t) { return t.id === tab; })) {
            return tab;
        }
        return cfg.activeTab || cfg.defaultTab || 'publications';
    }

    function CommunityTabs() {
        var initialTab = tabFromLocation();
        var state = useState(initialTab);
        var activeTab = state[0];
        var setActiveTab = state[1];

        var switchTab = useCallback(function (tabId, options) {
            options = options || {};
            if (!(cfg.tabs || []).some(function (t) { return t.id === tabId; })) {
                return;
            }
            setActiveTab(tabId);
            applyPaneVisibility(tabId);
            if (options.push !== false) {
                var url = new URL(window.location.href);
                url.searchParams.set('tab', tabId);
                if (!options.keepPost) {
                    url.searchParams.delete('post');
                }
                window.history.pushState({ tab: tabId }, '', url.toString());
            }
        }, [setActiveTab]);

        useEffect(function () {
            applyPaneVisibility(activeTab);
        }, [activeTab]);

        useEffect(function () {
            window.communityDetailSwitchTab = function (tabId, options) {
                switchTab(tabId, options || {});
            };

            function onPopState() {
                switchTab(tabFromLocation(), { push: false });
            }

            window.addEventListener('popstate', onPopState);
            return function () {
                window.removeEventListener('popstate', onPopState);
                delete window.communityDetailSwitchTab;
            };
        }, [switchTab]);

        return React.createElement(
            'ul',
            { className: 'nav nav-tabs community-detail-tabs', id: 'communityTabs', role: 'tablist' },
            (cfg.tabs || []).map(function (tab) {
                var isActive = activeTab === tab.id;
                var children = [
                    React.createElement('i', {
                        key: 'icon',
                        className: 'fa ' + tab.icon + ' mr-1',
                        'aria-hidden': true,
                    }),
                    tab.label,
                ];

                if (tab.count > 0) {
                    children.push(
                        React.createElement(
                            'span',
                            { key: 'count', className: 'badge badge-light ml-1' },
                            tab.count
                        )
                    );
                }

                if (tab.showNew) {
                    children.push(
                        React.createElement(
                            'span',
                            { key: 'new', className: 'badge badge-success ml-1 community-tab-new-badge' },
                            'New'
                        )
                    );
                }

                return React.createElement(
                    'li',
                    { key: tab.id, className: 'nav-item', role: 'presentation' },
                    React.createElement(
                        'button',
                        {
                            type: 'button',
                            className: 'nav-link community-detail-tab-btn' + (isActive ? ' active' : ''),
                            role: 'tab',
                            'aria-selected': isActive,
                            'aria-controls': (cfg.paneIds || {})[tab.id] || tab.id,
                            onClick: function () {
                                switchTab(tab.id);
                            },
                        },
                        children
                    )
                );
            })
        );
    }

    function communityInitials(name) {
        var parts = String(name || '').trim().split(/\s+/).filter(Boolean);
        if (parts.length >= 2) {
            return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
        }
        return String(name || '?').slice(0, 2).toUpperCase();
    }

    function OtherCommunitiesPanel() {
        var communities = cfg.otherCommunities || [];

        if (!communities.length) {
            return React.createElement(
                'div',
                { className: 'community-detail-sidebar-card community-detail-sidebar-card--other' },
                React.createElement(SidebarCardHead, {
                    icon: 'fa-users',
                    iconClass: 'community-detail-sidebar-card__icon--gold',
                    title: 'My other communities',
                    hint: 'Quick links to your other memberships.',
                }),
                React.createElement(
                    'div',
                    { className: 'community-detail-sidebar-card__body' },
                    React.createElement(
                        'p',
                        { className: 'community-other-empty mb-0' },
                        'You are not a member of any other communities.'
                    )
                )
            );
        }

        return React.createElement(
            'div',
            { className: 'community-detail-sidebar-card community-detail-sidebar-card--other' },
            React.createElement(SidebarCardHead, {
                icon: 'fa-users',
                iconClass: 'community-detail-sidebar-card__icon--gold',
                title: 'My other communities',
                hint: 'Quick links to your other memberships.',
            }),
            React.createElement(
                'div',
                { className: 'community-detail-sidebar-card__body community-other-list' },
                communities.map(function (community) {
                    return React.createElement(
                        'a',
                        {
                            key: community.id,
                            href: community.url,
                            className: 'community-other-card notranslate',
                            translate: 'no',
                        },
                        React.createElement(
                            'span',
                            {
                                className: 'community-other-card__avatar',
                                style: { backgroundColor: primaryColor },
                                'aria-hidden': true,
                            },
                            communityInitials(community.name)
                        ),
                        React.createElement(
                            'span',
                            { className: 'community-other-card__body' },
                            React.createElement(
                                'span',
                                { className: 'community-other-card__name' },
                                community.name
                            ),
                            community.description
                                ? React.createElement(
                                      'span',
                                      { className: 'community-other-card__desc' },
                                      community.description
                                  )
                                : null,
                            React.createElement(
                                'span',
                                { className: 'community-other-card__stats' },
                                React.createElement(
                                    'span',
                                    { className: 'community-other-card__stat' },
                                    React.createElement('i', { className: 'fa fa-book mr-1', 'aria-hidden': true }),
                                    community.publications,
                                    ' pubs'
                                ),
                                React.createElement(
                                    'span',
                                    { className: 'community-other-card__stat' },
                                    React.createElement('i', { className: 'fa fa-comments mr-1', 'aria-hidden': true }),
                                    community.forums,
                                    ' forums'
                                ),
                                React.createElement(
                                    'span',
                                    { className: 'community-other-card__stat' },
                                    React.createElement('i', { className: 'fa fa-users mr-1', 'aria-hidden': true }),
                                    community.members,
                                    ' members'
                                )
                            )
                        ),
                        React.createElement('i', {
                            className: 'fa fa-chevron-right community-other-card__arrow',
                            'aria-hidden': true,
                        })
                    );
                })
            )
        );
    }

    function SidebarCardHead(props) {
        return React.createElement(
            'div',
            { className: 'community-detail-sidebar-card__head' },
            React.createElement(
                'span',
                {
                    className:
                        'community-detail-sidebar-card__icon ' + (props.iconClass || 'community-detail-sidebar-card__icon--green'),
                    'aria-hidden': true,
                },
                React.createElement('i', { className: 'fa ' + props.icon })
            ),
            React.createElement(
                'div',
                null,
                React.createElement('h2', { className: 'community-detail-sidebar-card__title' }, props.title),
                props.hint
                    ? React.createElement('p', { className: 'community-detail-sidebar-card__hint' }, props.hint)
                    : null
            )
        );
    }

    var tabsRoot = document.getElementById('community-detail-tabs-root');
    if (tabsRoot) {
        ReactDOM.createRoot(tabsRoot).render(React.createElement(CommunityTabs));
    }

    var otherRoot = document.getElementById('community-other-communities-root');
    if (otherRoot) {
        ReactDOM.createRoot(otherRoot).render(React.createElement(OtherCommunitiesPanel));
    }
})();
