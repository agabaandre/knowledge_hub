<?php
namespace App\Repositories;

use App\Models\Country;
use App\Models\Expert;
use App\Models\ExpertType;
use App\Models\IscoClassification;
use App\Models\JobTitle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ExpertsRepository extends SharedRepo{

    /** @var string Cache key shared with OccupationsViewComposer and API lookups */
    public const CACHE_KEY_JOB_TITLES_ALL = 'occupations_unique_by_name_v1';

    private const CACHE_KEY_PREFIX_JOB_TITLES_BY_ISCO = 'job_titles_by_isco_';

    private function jobTitlesCacheTtlMinutes(): int
    {
        return (int) env('CACHE_EXPIRY_DURATION_MINUTES', 60 * 24);
    }

    public static function jobTitlesByIscoCacheKey(string $iscoId): string
    {
        return self::CACHE_KEY_PREFIX_JOB_TITLES_BY_ISCO.md5((string) $iscoId);
    }

    /**
     * All job titles (cached). Used by experts index, occupations views, and lookup API.
     */
    public function getAllJobTitlesCached()
    {
        return Cache::remember(
            self::CACHE_KEY_JOB_TITLES_ALL,
            $this->jobTitlesCacheTtlMinutes(),
            static function () {
                return JobTitle::query()
                    ->orderBy('name')
                    ->orderBy('id')
                    ->get()
                    ->unique(function (JobTitle $job) {
                        $name = trim((string) ($job->name ?? ''));

                        return $name === '' ? "\0empty" : Str::lower($name);
                    })
                    ->sortBy(fn (JobTitle $job) => Str::lower(trim((string) ($job->name ?? ''))))
                    ->values();
            }
        );
    }

    /**
     * Job titles for one ISCO code (cached) — avoids repeated heavy queries on admin experts AJAX.
     */
    public function getJobTitlesForIscoCached(string $iscoId)
    {
        $key = self::jobTitlesByIscoCacheKey($iscoId);

        return Cache::remember(
            $key,
            $this->jobTitlesCacheTtlMinutes(),
            static function () use ($iscoId) {
                return JobTitle::where('isco_id', $iscoId)->orderBy('name')->get();
            }
        );
    }

    public function get(Request $request,$return_array=false){

        $rows_count = ($request->rows)?$request->rows:20;
        $query    = Expert::with(['type', 'country', 'jobTitle', 'iscoClassification'])->orderBy('id','desc');

        if($request->term):
            $query->where(function($q) use ($request) {
                $q->where('first_name','like','%'.$request->term.'%')
                  ->orWhere('last_name','like','%'.$request->term.'%')
                  ->orWhere('job_title','like','%'.$request->term.'%')
                  ->orWhere('email','like','%'.$request->term.'%')
                  ->orWhere('occupation','like','%'.$request->term.'%')
                  ->orWhereIn('expert_type_id',
                      ExpertType::where('type_name','like','%'.$request->term.'%')->pluck('id'))
                  ->orWhereIn('country_id',
                      Country::where('name','like','%'.$request->term.'%')->pluck('id'));
            });
        endif;

        if($request->country):
            $query->where('country_id', $request->country);
        endif;

        if($request->expert_type_id):
            $query->where('expert_type_id', $request->expert_type_id);
        endif;

        if($request->export == 1){
            $this->excel_export($query);
            return;
        }
       
        $this->access_filter($query);

        $results = ($return_array)?$query->get():$query->paginate($rows_count)->appends($request->all());
        return $results;
    }

    private function excel_export($results){

        $export_file = 'experts-list-'.time().'.xls';
        $export_data = [];

        $results->chunk(100, function($records) use(&$export_data) {

            foreach ($records as $row){

               $data_row =  [
                "First Name"   => $row->first_name
                ,"Last Name"   => $row->last_name
                ,"Job"         =>$row->job_title
                ,"Occupation"  =>$row->occupation
                ,"Expert Type" =>$row->expert_type
                ,"Email"       => $row->email
                ,"Telephone"   =>$row->phone_number
                ,"Country"     =>($row->country)?$row->country->name:''
               ];

               array_push($export_data,$data_row);
            }

        });

       set_time_limit(0);

        $filename =  $export_file;      
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");

       export_excel($export_data);
    }

    
    public function save(Request $request){

        // Avoid null model: invalid id (e.g. "0", deleted row) made Expert::find() return null → fatal error on assign
        $expert = new Expert();
        if ($request->filled('id')) {
            $existing = Expert::find($request->id);
            if (!$existing) {
                throw new \InvalidArgumentException('Expert not found for the given ID. Refresh the page and try again.');
            }
            $expert = $existing;
        }

        $expert->first_name = $request->first_name;
        $expert->last_name  = $request->last_name;
        $expert->job_title  = $request->job_title ?: null;
        $expert->email      = $request->email;
        $expert->phone_number = $request->phone_number ?: null;
        $expert->expert_type_id = (int) $request->type_id;
        $expert->country_id = (int) $request->country_id;

        // These columns exist only after migration 2025_11_01_204914 — skip if DB not migrated yet
        if (Schema::hasColumn('experts', 'isco_classification_id')) {
            $expert->isco_classification_id = $request->filled('isco_classification_id')
                ? (int) $request->isco_classification_id
                : null;
        }
        if (Schema::hasColumn('experts', 'job_title_id')) {
            $expert->job_title_id = $request->filled('job_title_id')
                ? (int) $request->job_title_id
                : null;
        }

        if ($request->has('field')) {
            $expert->occupation = $request->field;
        }

        // Unlink job title when classification has no linked job titles, or job doesn't match ISCO
        if (Schema::hasColumn('experts', 'job_title_id') && Schema::hasColumn('experts', 'isco_classification_id')) {
            $expert->job_title_id = $this->resolveExpertJobTitleId(
                $expert->isco_classification_id,
                $expert->job_title_id
            );
        }

        $saved = $expert->save();

        return $saved ? $expert : false;
    }

    /**
     * Keep job_title_id only when it belongs to the selected classification's ISCO.
     * If the classification has no job titles, force null (unlink).
     */
    private function resolveExpertJobTitleId(?int $iscoClassificationId, ?int $jobTitleId): ?int
    {
        if (!$jobTitleId) {
            return null;
        }
        if (!$iscoClassificationId) {
            return $jobTitleId;
        }

        $classification = IscoClassification::find($iscoClassificationId);
        if (!$classification) {
            return null;
        }

        $jobsForIsco = JobTitle::where('isco_id', $classification->isco_id)->count();
        if ($jobsForIsco === 0) {
            return null;
        }

        $job = JobTitle::find($jobTitleId);
        if (!$job || (string) $job->isco_id !== (string) $classification->isco_id) {
            return null;
        }

        return $jobTitleId;
    }

    public function find($id){
        return Expert::find($id);
    }

    public function delete($id){

        return Expert::find($id)->delete();
    }

    public function get_types(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $query    = ExpertType::orderBy('id','desc');

        if($request->term):
            $query->where('type_name','like','%'.$request->term.'%');
        endif;
        $results = $query->paginate($rows_count);
        return $results;
    }

    public function save_type(Request $request){

        $expert_type = ($request->id)?ExpertType::find($request->id):new ExpertType();

        $expert_type->type_name = $request->type;
        $expert_type->type_desc  = $request->description;

        return ($request->id)?$expert_type->update():$expert_type->save();
    }

    public function delete_type($id){

        return ExpertType::find($id)->delete();
    }

    public function get_jobs(Request $request)
    {
        return $this->getAllJobTitlesCached();
    }



}