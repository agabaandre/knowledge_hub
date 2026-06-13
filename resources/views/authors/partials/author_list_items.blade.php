@foreach ($authors as $author)
    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-4">
        @php
            $isOrg = strtolower((string) ($author->is_organsiation ?? '')) === 'yes';
            $avatarUrl = null;
            if (! empty($author->logo) && $author->logo !== 'author.png') {
                $avatarUrl = filter_var($author->logo, FILTER_VALIDATE_URL) ? $author->logo : asset(ltrim($author->logo, '/'));
            } elseif ($author->user && ! empty($author->user->photo)) {
                $avatarUrl = $author->user->photo;
            }
            $cardId = 'contributor-card-'.$author->id;
        @endphp
        <article class="author-card" aria-labelledby="{{ $cardId }}">
            <div class="author-header">
                <div class="author-avatar">
                    @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $author->name }} — {{ $isOrg ? 'organisation' : 'contributor' }} profile" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa {{ $isOrg ? 'fa-building' : 'fa-user' }} author-avatar-icon\'></i>';">
                    @else
                        <i class="fa {{ $isOrg ? 'fa-building' : 'fa-user' }} author-avatar-icon" aria-hidden="true"></i>
                    @endif
                </div>
                <div class="author-info">
                    <h2 class="author-name notranslate" id="{{ $cardId }}" translate="no">
                        <a href="{{ author_publications_url($author) }}" title="View profile and publications for {{ $author->name }}" class="notranslate" translate="no">
                            @if (! empty($author->orcid))
                                <span title="View {{ $author->name }}'s ORCID profile" class="notranslate" translate="no">
                                    {{ truncate($author->name, 25) }}
                                    <i class="fa fa-external-link-alt" style="font-size: 0.7em; margin-left: 3px;"></i>
                                </span>
                            @else
                                {{ truncate($author->name, 25) }}
                            @endif
                        </a>
                    </h2>
                    @if ($author->user && $author->user->job_title)
                        <div class="author-title notranslate" translate="no">
                            <i class="fa fa-briefcase me-1" style="font-size: 0.8em;"></i>
                            {{ truncate($author->user->job_title, 30) }}
                        </div>
                    @endif
                    @if ($author->user && $author->user->organization_name)
                        <div class="author-organization notranslate" translate="no">
                            <i class="fa fa-building me-1" style="font-size: 0.8em;"></i>
                            {{ truncate($author->user->organization_name, 30) }}
                        </div>
                    @endif
                    @if ($author->user && $author->user->country && $author->user->country->name)
                        <div class="author-organization notranslate" translate="no">
                            <i class="fa fa-map-marker-alt me-1" style="font-size: 0.8em;"></i>
                            {{ $author->user->country->name }}
                        </div>
                    @endif
                    @if ($author->user && $author->user->lifetimeBadge && $author->user->lifetimeBadge->badgeType)
                        @php
                            $lb = $author->user->lifetimeBadge;
                            $bt = $lb->badgeType;
                            $acquiredAt = $lb->last_upgraded_at ?? $lb->created_at;
                            $badgeTitle = $bt->name.' — '.number_format((int) $lb->lifetime_contributions).' lifetime contributions';
                            if ($acquiredAt) {
                                $badgeTitle .= ' · Acquired '.$acquiredAt->format('M j, Y');
                            }
                        @endphp
                        <div class="author-badges">
                            <span class="author-badge"
                                  style="background-color: {{ $bt->badge_color ?? '#C0C0C0' }};"
                                  title="{{ $badgeTitle }}">
                                {{ participant_badge_emoji($bt->slug ?? null) }}
                                {{ $bt->name }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="author-stats">
                <span class="author-resources">
                    <i class="fa fa-book"></i>
                    @php
                        $publicationCount = (int) ($author->publications_count ?? 0);
                        $forumEngagementCount = (int) ($author->forum_engagement_total ?? 0);
                        $totalContributions = (int) ($author->total_contributions ?? ($publicationCount + $forumEngagementCount));
                    @endphp
                    {{ $totalContributions }} {{ $totalContributions == 1 ? 'Contribution' : 'Contributions' }}
                </span>
                <a href="{{ author_publications_url($author) }}" class="view-link" title="View all resources by {{ $author->name }}">
                    View profile <i class="fa fa-arrow-right ms-1" aria-hidden="true"></i>
                </a>
            </div>
        </article>
    </div>
@endforeach
