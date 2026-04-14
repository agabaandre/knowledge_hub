<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicationStaging extends Model
{
    protected $table = 'publications_staging';

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'rss_feed_id',
        'rss_guid',
        'rss_link',
        'processed_at',
        'processed_status',
        'rejection_reason',
        'publication_id',
        'openai_metadata',
        'title',
        'description',
        'publication',
        'author_id',
        'associated_authors',
        'author_affiliation',
        'sub_thematic_area_id',
        'publication_catgory_id',
        'data_category_id',
        'file_type_id',
        'geographical_coverage_id',
        'geographical_scope_id',
        'year_published',
        'doi',
        'issn',
        'isbn',
        'publisher',
        'license_id',
        'copyright_info',
        'funder',
        'journal_name',
        'journal_volume',
        'journal_issue',
        'journal_pages',
        'cover',
        'cover_is_exteranl',
        'citation_authors',
        'citation_link',
        'is_active',
        'is_admin_only_access',
        'is_approved',
        'is_rejected',
        'is_embedded',
        'is_featured',
        'is_version',
        'is_video',
        'show_disclaimer',
        'is_default_in_category',
        'parent_id',
        'version_no',
        'visits',
        'user_id',
        'date_created',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'openai_metadata' => 'array',
        'date_created' => 'date',
    ];

    public function feed(): BelongsTo
    {
        return $this->belongsTo(RssFeed::class, 'rss_feed_id');
    }

    public function publicationRecord(): BelongsTo
    {
        return $this->belongsTo(Publication::class, 'publication_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    public function isPending(): bool
    {
        return $this->processed_status === self::STATUS_PENDING;
    }
}
