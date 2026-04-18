<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Validation rules and messages for publication create/update (web wizard and API).
 * Mirrors {@see \App\Http\Controllers\AccountController::submit_publication()}.
 */
final class PublicationSubmissionValidation
{
    public static function rules(Request $request): array
    {
        $minWords = settings()->publication_min_words ?? 150;
        $minChars = $minWords * 5;

        $requiredFields = json_decode(settings()->publication_required_fields ?? '{}', true);
        if (empty($requiredFields) || ! is_array($requiredFields)) {
            $requiredFields = [
                'title' => true,
                'description' => true,
                'associated_authors' => true,
                'tags' => true,
                'theme' => true,
                'sub_theme' => true,
                'data_category_id' => true,
            ];
        }

        $isAdmin = false;
        $authUser = auth('api')->user() ?? auth()->user();
        if ($authUser) {
            $role = \get_role($authUser->id);
            $isAdmin = ($role && strpos(strtolower($role->name), 'admin') !== false);
        }

        $val_rules = [
            'title' => ($requiredFields['title'] ?? true) ? 'required|string|max:500' : 'nullable|string|max:500',
            'description' => ($requiredFields['description'] ?? true) ? 'required|string|min:'.$minChars : 'nullable|string|min:'.$minChars,
            'associated_authors' => ($requiredFields['associated_authors'] ?? true) ? 'required|string|max:500' : 'nullable|string|max:500',
            'author_affiliation' => 'required|string|max:500',
            'tags' => ($requiredFields['tags'] ?? true) ? 'required|array|min:1' : 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'theme' => ($requiredFields['theme'] ?? true) ? 'required' : 'nullable',
            'sub_theme' => ($requiredFields['sub_theme'] ?? true) ? 'required' : 'nullable',
            'data_category_id' => ($requiredFields['data_category_id'] ?? true) ? 'required' : 'nullable',
            'year_published' => ($requiredFields['year_published'] ?? false) ? 'required|integer|min:1900|max:'.date('Y') : 'nullable|integer|min:1900|max:'.date('Y'),
            'author' => ($isAdmin && ($requiredFields['author'] ?? false))
                ? 'required|exists:author,id'
                : 'nullable|exists:author,id',
            'doi' => ($requiredFields['doi'] ?? false) ? 'required|string|max:255' : 'nullable|string|max:255',
            'issn' => ($requiredFields['issn'] ?? false) ? 'required|string|max:50' : 'nullable|string|max:50',
            'isbn' => ($requiredFields['isbn'] ?? false) ? 'required|string|max:50' : 'nullable|string|max:50',
            'license_id' => ($requiredFields['license_id'] ?? false) ? 'required|exists:licenses,id' : 'nullable|exists:licenses,id',
            'copyright_info' => ($requiredFields['copyright_info'] ?? false) ? 'required|string' : 'nullable|string',
            'countries' => 'nullable|array',
            'countries.*' => 'exists:country,id',
            'category_id' => 'nullable|integer',
            'publication_sub_category_id' => 'nullable|integer',
            'rccs' => 'nullable|array',
            'communities' => 'nullable|array',
            'upload_type' => 'nullable|in:upload,link',
            'link' => 'nullable|string|max:2048',
            'is_embedded' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'admin_only' => 'nullable|boolean',
            'show_disclaimer' => 'nullable|boolean',
            'tag_all_my_communities' => 'nullable|boolean',
            'publisher' => 'nullable|string|max:500',
            'funder' => 'nullable|string|max:500',
            'journal_name' => 'nullable|string|max:500',
            'journal_volume' => 'nullable|string|max:100',
            'journal_issue' => 'nullable|string|max:100',
            'journal_pages' => 'nullable|string|max:100',
            'cover' => 'nullable|file|image|max:10240',
            'cover_url' => 'nullable|string|max:2048',
            'files' => 'nullable',
            'remove_attachments' => 'nullable|array',
            'remove_attachments.*' => 'integer|exists:publication_attachments,id',
        ];

        if ($request->upload_type == 'link') {
            $val_rules['link'] = 'required|url';
        }

        if ($request->original_id) {
            unset($val_rules['sub_theme'], $val_rules['title']);
        }

        if ($request->id) {
            unset($val_rules['cover']);
        }

        return $val_rules;
    }

    public static function messages(Request $request): array
    {
        $minWords = settings()->publication_min_words ?? 150;
        $minChars = $minWords * 5;

        return [
            'data_category_id.required' => 'Please select a category for your resource.',
            'theme.required' => 'Please select a thematic area.',
            'sub_theme.required' => 'Please select a sub-theme.',
            'title.required' => 'A resource title is required. Please provide a clear, descriptive title.',
            'title.max' => 'The title cannot exceed 500 characters.',
            'description.required' => 'A description is required. Please describe your resource in detail.',
            'description.min' => 'The description must be at least '.$minWords.' words (approximately '.$minChars.' characters). Please provide more details about your resource.',
            'associated_authors.required' => 'Associated authors are required. Please list the authors or co-authors.',
            'associated_authors.max' => 'Associated authors cannot exceed 500 characters.',
            'author_affiliation.required' => 'Author affiliation/institution is required. Please enter the institution or organization of the authors.',
            'author_affiliation.max' => 'Author affiliation cannot exceed 500 characters.',
            'tags.required' => 'Please select at least one tag/health topic to help categorize your publication.',
            'tags.min' => 'Please select at least one tag/health topic.',
            'tags.*.exists' => 'One or more selected tags are invalid.',
            'year_published.required' => 'Year published is required.',
            'author.required' => 'Source/Author is required.',
            'author.exists' => 'author must be a valid author id (author.id = users.author_id for the intended account). Use GET /api/lookup/authors for ids. On POST /api/publications, non-admins are always assigned their own users.author_id.',
            'doi.required' => 'DOI is required.',
            'issn.required' => 'ISSN is required.',
            'isbn.required' => 'ISBN is required.',
            'license_id.required' => 'License is required.',
            'copyright_info.required' => 'Copyright information is required.',
            'countries.required' => 'Please select at least one member state.',
            'countries.array' => 'Please select at least one member state.',
            'countries.min' => 'Please select at least one member state.',
            'link.required' => 'Please provide the external link URL for your resource.',
            'link.url' => 'Please provide a valid URL (starting with http:// or https://).',
        ];
    }
}
