<div class="tab-pane fade" id="search" role="tabpanel">
    <div class="form-section-title">
        <i class="fa fa-search"></i>
        Search &amp; Listings
    </div>

    <div class="row settings-grid-row">
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="enable_ai_search" name="enable_ai_search" value="1" @if($settings->enable_ai_search ?? 1) checked @endif>
                    <label class="form-check-label" for="enable_ai_search">Enable AI Search</label>
                </div>
                <small class="info-text">Show AI Search in the search bar.</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="auto_approve_comments" name="auto_approve_comments" value="1" @if($settings->auto_approve_comments ?? 1) checked @endif>
                    <label class="form-check-label" for="auto_approve_comments">Auto-approve comments</label>
                </div>
                <small class="info-text">Forum and publication comments.</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="auto_approve_publications" name="auto_approve_publications" value="1" @if($settings->auto_approve_publications ?? 0) checked @endif>
                    <label class="form-check-label" for="auto_approve_publications">Auto-approve publications</label>
                </div>
                <small class="info-text">When enabled, admin and configured roles publish without moderation.</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="enable_ai_chat_prune" name="enable_ai_chat_prune" value="1" @if($settings->enable_ai_chat_prune ?? 1) checked @endif>
                    <label class="form-check-label" for="enable_ai_chat_prune">Scheduled AI chat cleanup</label>
                </div>
            </div>
        </div>
    </div>

    <div class="form-section-title mt-4">
        <i class="fa fa-list"></i>
        Records Search Page
    </div>

    <div class="row settings-grid-row">
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="search_show_forums" name="search_show_forums" value="1" @if($settings->search_show_forums ?? true) checked @endif>
                    <label class="form-check-label" for="search_show_forums">Show forums in search</label>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="search_show_communities" name="search_show_communities" value="1" @if($settings->search_show_communities ?? true) checked @endif>
                    <label class="form-check-label" for="search_show_communities">Show communities in search</label>
                </div>
            </div>
        </div>
        @if(Schema::hasColumn('setting', 'show_publication_card_file_type_badge'))
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <input type="hidden" name="show_publication_card_file_type_badge" value="0">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="show_publication_card_file_type_badge" name="show_publication_card_file_type_badge" value="1" @if($settings->show_publication_card_file_type_badge ?? true) checked @endif>
                    <label class="form-check-label" for="show_publication_card_file_type_badge">File type icon on cards</label>
                </div>
            </div>
        </div>
        @endif
    </div>

  @if(Schema::hasColumn('setting', 'communities_listing_show_participants') || Schema::hasColumn('setting', 'communities_listing_max_faces') || Schema::hasColumn('setting', 'communities_listing_cards_per_row'))
    <div class="form-section-title mt-4">
        <i class="fa fa-users"></i>
        Communities Listing
    </div>
    <div class="row settings-grid-row">
        @if(Schema::hasColumn('setting', 'communities_listing_show_participants'))
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <input type="hidden" name="communities_listing_show_participants" value="0">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="communities_listing_show_participants" name="communities_listing_show_participants" value="1" @if($settings->communities_listing_show_participants ?? true) checked @endif>
                    <label class="form-check-label" for="communities_listing_show_participants">Show participants on cards</label>
                </div>
            </div>
        </div>
        @endif
        @if(Schema::hasColumn('setting', 'communities_listing_max_faces'))
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label for="communities_listing_max_faces">Max participant faces</label>
                <input type="number" class="form-control" id="communities_listing_max_faces" name="communities_listing_max_faces" min="1" max="24" value="{{ (int) ($settings->communities_listing_max_faces ?? 8) }}">
            </div>
        </div>
        @endif
        @if(Schema::hasColumn('setting', 'communities_listing_cards_per_row'))
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label for="communities_listing_cards_per_row">Cards per row (desktop)</label>
                <select class="form-control" id="communities_listing_cards_per_row" name="communities_listing_cards_per_row">
                    @foreach([1 => '1 per row', 2 => '2 per row (default)', 3 => '3 per row'] as $val => $label)
                        <option value="{{ $val }}" @if((int) ($settings->communities_listing_cards_per_row ?? 2) === $val) selected @endif>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif
    </div>
  @endif

    <div class="form-section-title mt-4">
        <i class="fa fa-arrows-alt"></i>
        Pagination &amp; Infinite Scroll
    </div>
    <small class="info-text mb-3 d-block">How listings load more content across the site.</small>

    <div class="row settings-grid-row">
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label for="search_pagination_mode">Records search</label>
                <select class="form-control" id="search_pagination_mode" name="search_pagination_mode">
                    <option value="pagination" @if(($settings->search_pagination_mode ?? 'pagination') === 'pagination') selected @endif>Pagination</option>
                    <option value="infinite_scroll" @if(($settings->search_pagination_mode ?? 'pagination') === 'infinite_scroll') selected @endif>Infinite scroll</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label for="forums_pagination_mode">Forums</label>
                <select class="form-control" id="forums_pagination_mode" name="forums_pagination_mode">
                    <option value="pagination" @if(($settings->forums_pagination_mode ?? 'pagination') === 'pagination') selected @endif>Pagination</option>
                    <option value="infinite_scroll" @if(($settings->forums_pagination_mode ?? 'pagination') === 'infinite_scroll') selected @endif>Infinite scroll</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label for="communities_pagination_mode">Communities</label>
                <select class="form-control" id="communities_pagination_mode" name="communities_pagination_mode">
                    <option value="pagination" @if(($settings->communities_pagination_mode ?? 'infinite_scroll') === 'pagination') selected @endif>Pagination</option>
                    <option value="infinite_scroll" @if(($settings->communities_pagination_mode ?? 'infinite_scroll') === 'infinite_scroll') selected @endif>Infinite scroll</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row settings-grid-row mt-2">
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label for="courses_pagination_mode">Courses</label>
                <select class="form-control" id="courses_pagination_mode" name="courses_pagination_mode">
                    <option value="pagination" @if(($settings->courses_pagination_mode ?? 'infinite_scroll') === 'pagination') selected @endif>Pagination</option>
                    <option value="infinite_scroll" @if(($settings->courses_pagination_mode ?? 'infinite_scroll') === 'infinite_scroll') selected @endif>Infinite scroll</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label for="faqs_pagination_mode">FAQs</label>
                <select class="form-control" id="faqs_pagination_mode" name="faqs_pagination_mode">
                    <option value="pagination" @if(($settings->faqs_pagination_mode ?? 'infinite_scroll') === 'pagination') selected @endif>Pagination</option>
                    <option value="infinite_scroll" @if(($settings->faqs_pagination_mode ?? 'infinite_scroll') === 'infinite_scroll') selected @endif>Infinite scroll</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label for="health_topics_pagination_mode">Health topics</label>
                <select class="form-control" id="health_topics_pagination_mode" name="health_topics_pagination_mode">
                    <option value="pagination" @if(($settings->health_topics_pagination_mode ?? 'infinite_scroll') === 'pagination') selected @endif>Pagination</option>
                    <option value="infinite_scroll" @if(($settings->health_topics_pagination_mode ?? 'infinite_scroll') === 'infinite_scroll') selected @endif>Infinite scroll</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row settings-grid-row mt-2">
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label for="home_events_pagination_mode">Homepage events</label>
                <select class="form-control" id="home_events_pagination_mode" name="home_events_pagination_mode">
                    <option value="pagination" @if(($settings->home_events_pagination_mode ?? 'infinite_scroll') === 'pagination') selected @endif>Fixed batch (12)</option>
                    <option value="infinite_scroll" @if(($settings->home_events_pagination_mode ?? 'infinite_scroll') === 'infinite_scroll') selected @endif>Infinite scroll</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label for="authors_pagination_mode">Contributors</label>
                <select class="form-control" id="authors_pagination_mode" name="authors_pagination_mode">
                    <option value="pagination" @if(($settings->authors_pagination_mode ?? 'infinite_scroll') === 'pagination') selected @endif>Pagination</option>
                    <option value="infinite_scroll" @if(($settings->authors_pagination_mode ?? 'infinite_scroll') === 'infinite_scroll') selected @endif>Infinite scroll</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label for="country_publications_pagination_mode">Country publications</label>
                <select class="form-control" id="country_publications_pagination_mode" name="country_publications_pagination_mode">
                    <option value="pagination" @if(($settings->country_publications_pagination_mode ?? 'infinite_scroll') === 'pagination') selected @endif>Pagination</option>
                    <option value="infinite_scroll" @if(($settings->country_publications_pagination_mode ?? 'infinite_scroll') === 'infinite_scroll') selected @endif>Infinite scroll</option>
                </select>
            </div>
        </div>
    </div>
</div>
