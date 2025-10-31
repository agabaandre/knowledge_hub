<?php

if (!function_exists('is_field_required')) {
    /**
     * Check if a publication form field is required based on admin settings
     *
     * @param string $fieldName
     * @return bool
     */
    function is_field_required($fieldName)
    {
        $requiredFields = json_decode(settings()->publication_required_fields ?? '{}', true);
        
        if (empty($requiredFields)) {
            // Default required fields
            $defaultRequired = ['title', 'description', 'associated_authors', 'tags', 'theme', 'sub_theme', 'data_category_id'];
            return in_array($fieldName, $defaultRequired);
        }
        
        return $requiredFields[$fieldName] ?? false;
    }
}

if (!function_exists('get_publication_min_words')) {
    /**
     * Get the minimum word count for publication descriptions
     *
     * @return int
     */
    function get_publication_min_words()
    {
        return settings()->publication_min_words ?? 150;
    }
}

