<?php

return [

    /*
    | Setting row keys exported/imported for mobile branding (GET /api/federation/lookup/settings).
    | Secrets and installer flags are excluded.
    */
    'branding_setting_keys' => [
        'site_name', 'title', 'slogan', 'site_description', 'seo_keywords', 'content_disclaimer',
        'primary_color', 'secondary_color', 'default_primary_color', 'default_secondary_color',
        'gradient_start_color', 'gradient_end_color', 'icon_font_color', 'primary_text_color',
        'links_active_color', 'logo_scale', 'header_logo_inverse', 'footer_logo_inverse',
        'au_red', 'au_gold', 'au_corporate_green', 'au_green', 'au_plum', 'au_grey_text', 'au_white',
        'nav_style', 'nav_link_color', 'nav_link_hover_color', 'nav_link_active_color', 'nav_font_weight',
        'footer_style', 'site_theme', 'language', 'timezone',
        'show_featured', 'show_top_searches', 'show_events', 'show_tags', 'show_health_themes', 'show_quotes',
        'enable_ai_search', 'search_show_forums', 'search_show_communities',
        'section_title_recommended', 'section_title_top_searches', 'section_title_flagship_initiatives',
        'section_title_health_themes', 'theme_cards_per_row', 'theme_card_opacity',
        'menu_icons_enabled', 'publication_min_words', 'show_publication_card_file_type_badge',
    ],

    /*
    | Image fields resolved to absolute URLs in federation branding export.
    */
    'branding_image_keys' => [
        'logo', 'favicon', 'spotlight_banner',
    ],

    /*
    | Lookup metadata tables included in GET /api/federation/lookup/metadata.
    */
    'metadata_tables' => [
        'thematic_areas',
        'sub_thematic_areas',
        'tags',
        'resource_types',
        'publication_categories',
        'licenses',
        'static_links',
    ],

    /*
    | OAuth-style federation tokens (child hub ↔ central hub).
    | Child hubs exchange the central hub's registration token once, then refresh automatically.
    */
    'access_token_ttl_seconds' => (int) env('FEDERATION_ACCESS_TOKEN_TTL', 86400),
    'refresh_token_ttl_seconds' => (int) env('FEDERATION_REFRESH_TOKEN_TTL', 7776000),
    'refresh_buffer_seconds' => (int) env('FEDERATION_REFRESH_BUFFER', 300),

];
