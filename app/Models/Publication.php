<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Illuminate\Support\Facades\DB;

class Publication extends Model
{
    use HasFactory;
    use Searchable;


    protected $table = "publication";
    protected $guarded =[];
    protected $appends = ['theme','label','value','is_favourite','approved_comments',
    'pending_comments','has_attachments','tag_ids','image_url',
    'publication_countries','publication_regions','country_ids','region_ids'];
    protected $dates = ['created_at', 'updated_at', 'date_created'];

  
    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'sub_thematic_area_id' => $this->sub_thematic_area_id,
            'publication_catgory_id' => $this->publication_catgory_id,
            'data_category_id' => $this->data_category_id
        ];
    }

    public function file_type(){
        return $this->belongsTo(PublicationType::class,"file_type_id","id");
    }

    public function sub_category(){
        return $this->belongsTo(PublicationCategory::class,"data_category_id","id");
    }

    public function publication_sub_category(){
        return $this->belongsTo(PublicationCategory::class, 'publication_sub_category_id', 'id');
    }

    public function data_category(){
        return $this->belongsTo(DataCategory::class, "publication_catgory_id","id");
    }

    public function category(){
        return $this->belongsTo(DataCategory::class, "publication_catgory_id","id");
    }


    public function attachments(){
        return $this->hasMany(PublicationAttachment::class);
    }

    public function getHasAttachmentsAttribute(){
        return count($this->attachments)>0;
    }


    public function tags(){
        return $this->hasMany(PublicationTag::class, 'publication_id', 'id');
    }

    public function monthlyViews(){
        return $this->hasMany(PublicationView::class, 'publication_id', 'id');
    }

    public function author(){
        return $this->belongsTo(Author::class);
    }

    public function sub_theme(){
        return $this->belongsTo(SubThemeticArea::class,"sub_thematic_area_id","id");
    }

    public function license(){
        return $this->belongsTo(License::class);
    }


    public function getThemeAttribute(){

        $minutes = env('CACHE_EXPIRY_DURATION_MINUTES',60*24);

        $theme = cache()->remember('Theme'.$this->id,$minutes, function () {
            return  @$this->sub_theme->theme;
        });

        return $theme;
    }

    public function getLabelAttribute(){
        return substr(cleanUTF8($this->title),0,100);
    }


    public function getValueAttribute(){
        return substr(cleanUTF8($this->title),0,100);
    }

    public function comments(){
        return $this->hasMany(PublicationComment::class);
    }

    public function versioning(){
        return $this->hasMany(Publication::class,'parent_id','id');
    }

    public function parent(){
        return $this->belongsTo(Publication::class,'id','parent_id');
    }

    public function getIsFavouriteAttribute(){
        if(!auth()->user())
        return false;
        
        $fav = Favourite::where('user_id',auth()->user()->id)->where('publication_id',$this->id)->first();
        return ($fav)?true:false;
    }

    public function summaries(){
        return $this->hasMany(PublicationSummary::class,"resource_id");
    }

    public function getApprovedCommentsAttribute(){
        $comments = PublicationComment::where('publication_id',$this->id)
        ->where('status','approved')
        ->get();
        return $comments;
    }

    public function getPendingCommentsAttribute(){
        $comments = PublicationComment::where('publication_id',$this->id)
        ->where('status','pending')
        ->get();
        return $comments;
    }

    public function accessgroups(){

        return $this->hasManyThrough(
            UserAccessGroup::class,
            PublicationAccessGroup::class,
            'publication_id', // Foreign key on PublicationAccessGroup table.
            'id', // Foreign key on UserAccessGroup table. Assuming 'id' is the primary key.
            'id', // Local key on Publication table.
            'user_access_group_id' // Local key on PublicationAccessGroup table that relates to UserAccessGroup.
        );
    }

    public function communities(){
       
        return $this->hasManyThrough(
            CommunityOfPractice::class, //get access to these
            PublicationCommunityOfPractice::class, //thru these
            'publication_id', // Foreign key on PublicationCommunityOfPractice table.
            'id', // Foreign key on UserAccessGroup table. Assuming 'id' is the primary key.
            'id', // Local key on Publication table.
            'community_of_practice_id' // Local key on PublicationCommunityOfPractice table that relates to UserAccessGroup.
        );
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function approver(){
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function rejector(){
        return $this->belongsTo(User::class, 'rejected_by', 'id');
    }

    public function favourited(){
        return $this->hasMany(Favourite::class);
    }


    public function getTagIdsAttribute(){
        try {
            $tagIds = DB::table('publication_tags')
                ->where('publication_id', $this->id)
                ->pluck('tag_id')
                ->toArray();
            
            // Log for debugging
            \Log::debug('Tag IDs accessor called', [
                'publication_id' => $this->id,
                'tag_ids_count' => count($tagIds),
                'tag_ids' => $tagIds
            ]);
            
            return $tagIds;
        } catch (\Exception $e) {
            \Log::error('Error getting tag_ids: ' . $e->getMessage(), [
                'publication_id' => $this->id
            ]);
            return [];
        }
    }

    public function country()
    {
       return  $this->belongsTo(Country::class,"geographical_coverage_id","id");
    }

    public function getImageUrlAttribute(){
        return $this->cover;
    }

    public function getCoverAttribute($value){
        // Return null if value is empty or null
        if (empty($value) || $value === null) {
            return null;
        }
        
        // If external URL, return as is
        if ($this->cover_is_exteranl) {
            return $value;
        }
        
        // Local file - use storage_link helper
        return storage_link('uploads/publications/'.$value);
    }

    public function getPublicationAttribute($value)
    {
        return cleanUTF8($value);
    }

    /**
     * Full URL for the publication PDF (for ChatPDF add-url). Returns null if not a PDF.
     */
    public function getPublicationPdfUrlAttribute()
    {
        $raw = $this->getRawOriginal('publication');
        if (empty($raw) || !publication_filename_is_pdf($raw)) {
            return null;
        }
        if (strpos($raw, 'http://') === 0 || strpos($raw, 'https://') === 0) {
            return $raw;
        }
        $resolved = resolve_publication_upload_disk_path($raw);
        if ($resolved) {
            return storage_link('uploads/publications/' . basename($resolved));
        }
        $forUrl = normalize_publication_stored_filename_for_public_url($raw) ?? $raw;

        return storage_link('uploads/publications/' . $forUrl);
    }

    /**
     * Absolute path to the publication PDF file on disk (for ChatPDF add-file when URL is not public).
     */
    public function getPublicationPdfPathAttribute()
    {
        $raw = $this->getRawOriginal('publication');
        if (empty($raw) || !publication_filename_is_pdf($raw)) {
            return null;
        }
        if (strpos($raw, 'http://') === 0 || strpos($raw, 'https://') === 0) {
            return null; // external URL, no local path
        }

        return resolve_publication_upload_disk_path($raw);
    }

    /**
     * Whether this publication has at least one PDF (main file or any attachment).
     */
    public function getHasAnyPdfAttribute()
    {
        if ($this->publication_pdf_url || $this->publication_pdf_path) {
            return true;
        }
        foreach ($this->attachments ?? [] as $att) {
            if ($att->is_pdf) {
                return true;
            }
        }
        return false;
    }

    /**
     * List of PDF sources for Chat with PDF: main (if PDF) + each PDF attachment.
     * Each item: ['type' => 'main'|'attachment', 'label' => string, 'attachment_id' => null|int]
     */
    public function getPdfSourcesAttribute()
    {
        $sources = [];
        if ($this->publication_pdf_url || $this->publication_pdf_path) {
            $sources[] = ['type' => 'main', 'label' => 'Main document', 'attachment_id' => null];
        }
        foreach ($this->attachments ?? [] as $att) {
            if ($att->is_pdf) {
                $label = $att->original_filename ?? $att->description ?? pathinfo($att->getRawOriginal('file'), PATHINFO_FILENAME);
                $label = \Illuminate\Support\Str::limit(str_replace('_', ' ', $label), 80);
                $sources[] = ['type' => 'attachment', 'label' => $label, 'attachment_id' => $att->id];
            }
        }
        return $sources;
    }

    // Ensure the content is UTF-8 encoded
    public function getDescriptionAttribute($value)
    {
        return  $value;
    }

    public function getTitleAttribute($value)
    {
        return cleanUTF8($value);
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'publication_countries', 'publication_id', 'country_id');
    }

    public function getPublicationCountriesAttribute(){
        return $this->countries()->pluck('name')->implode(', ');
    }

    public function getCountryIdsAttribute(){
        return DB::table('publication_countries')
            ->where('publication_id', $this->id)
            ->pluck('country_id')
            ->toArray();
    }

    public function getPublicationRegionsAttribute(){
        $countryIds = $this->getCountryIdsAttribute();
        if (empty($countryIds)) {
            return '';
        }
        $regionIds = Country::whereIn('id', $countryIds)->distinct()->pluck('region_id')->filter()->toArray();
        return Region::whereIn('id', $regionIds)->pluck('region_name')->implode(', ');
    }

    public function getRegionIdsAttribute(){
        $countryIds = $this->getCountryIdsAttribute();
        if (empty($countryIds)) {
            return [];
        }
        $regionIds = Country::whereIn('id', $countryIds)->distinct()->pluck('region_id')->filter()->toArray();
        return array_values($regionIds);
    }

    public function scopeSearchTerm($query, $term)
    {
        $term = trim((string) $term);
        if ($term !== '') {
            // Escape LIKE wildcards so literal % and _ don't break search intent
            $safeTerm = addcslashes($term, '%_');
            $query->where(function ($q) use ($term, $safeTerm) {
                $q->where('title', 'like', '%' . $safeTerm . '%')
                    ->orWhere('description', 'like', '%' . $safeTerm . '%')
                    ->orWhereIn('author_id', Author::where('name', 'like', '%' . $safeTerm . '%')->pluck('id'))
                    // Search in associated authors (full phrase, e.g. "Beyande Raissa")
                    ->orWhere('associated_authors', 'like', '%' . $safeTerm . '%');

                // Multi-word author search: match when all words appear in associated_authors (any order)
                $words = array_filter(preg_split('/\s+/u', trim($term), -1, PREG_SPLIT_NO_EMPTY), function ($w) {
                    return strlen($w) > 1;
                });
                if (count($words) > 1) {
                    $q->orWhere(function ($sub) use ($words) {
                        foreach ($words as $w) {
                            $sub->where('associated_authors', 'like', '%' . $w . '%');
                        }
                    });
                }

                if (states_enabled()) {
                    $q->orWhereIn('geographical_coverage_id', GeoCoverage::where('name', 'like', '%' . $safeTerm . '%')->pluck('id'));
                }
            });
        }
    }

    public function scopeFeatured($query, $subthemes)
    {
        if (count($subthemes) > 0) {
            $query->whereHas('sub_theme', function ($q) use ($subthemes) {
                $q->whereIn('id', $subthemes);
            });
        } else {
            $query->where('is_featured', 1);
        }
    }

    protected static function booted()
    {
        static::created(function ($publication) {
            \Illuminate\Support\Facades\Cache::flush();
        });
        static::updated(function ($publication) {
            \Illuminate\Support\Facades\Cache::flush();
        });
    }


}
