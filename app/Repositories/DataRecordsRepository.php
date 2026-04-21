<?php
namespace App\Repositories;

use App\Models\AssetType;
use App\Models\Country;
use App\Models\DataCategory;
use App\Models\DataRecord;
use App\Models\DataSubCategory;
use App\Models\PublicationCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DataRecordsRepository extends SharedRepo{

    public function get(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $results    = DataRecord::orderBy('id','desc');

       
        if($request->slug){
          $category       = DataCategory::where('slug',$request->slug)->first();
          $results->where('data_category_id',$category->id);
        }

        if($request->term){

         $results->where('title','like','%'.$request->term.'%');
         $results->orWhere('description','like','%'.$request->term.'%');
        }
        

        if($request->rcc)
            $results->whereIn('country_id',Country::where('region_id',$request->rcc)->pluck('id'));
        
        if($request->country_id)
            $results->where('country_id',$request->country_id);
      
        if($request->export == 1){
            $this->excel_export($results);
            return;
        }

        $this->access_filter($results);

        return $results->paginate($rows_count);
    }

    
    private function excel_export($results){

        $export_file = 'data-records-'.time().'.xls';
        $export_data = [];

        $results->chunk(100, function($records) use(&$export_data) {

            foreach ($records as $row){

               $data_row =  [
                "Title"   => $row->title
                ,"Category"   => $row->sub_category->category->category_name ?? ''
                ,"Description"   => $row->description
                //,"Url"=>$row->url
                ,"Country"  =>($row->country)?$row->country->name:''
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
        $record = new DataRecord();

        $record->title       = $request->title;
        $record->description = $request->description;
        $record->url = $request->link;
        $record->data_category_id =$request->data_category_id;;
        $record->data_sub_category_id = $request->data_sub_category_id;
        $record->country_id   = $request->country_id;
        $record->file_type_id = $request->file_type_id;
        $record->is_embedded  = (@$request->embedded)?1:0;

        return $record->save();
    }

    public function find($id){

        return DataRecord::find($id);
    }

    public function delete($id){
        return DataRecord::find($id)->delete();
    }

    public function get_categories(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $results    = DataCategory::orderBy('id','desc');

        return $results->paginate($rows_count);
    }

    public function get_subcategories(Request $request){

        $rows_count = ($request->rows)?$request->rows:20;
        $results    = DataSubCategory::orderBy('id','desc');

        return $results->paginate($rows_count);
    }

    public function get_json_countries() {
        $results = Country::all();
        return $results;
    }

    public function get_json_categories()
    {
        $results = DataCategory::all();
        return $results;
    }

    public function delete_category($id){

        return DataCategory::find($id)->delete();
    }

    public function allCategoriesForMapping()
    {
        return DataCategory::query()->orderBy('category_name')->get(['id', 'category_name']);
    }

    public function deleteCategoryWithMapping(int $id, int $replacementCategoryId): array
    {
        if ($id === $replacementCategoryId) {
            return ['status' => 'failure', 'message' => 'Please select a different category to map data to.'];
        }
        $old = DataCategory::find($id);
        $new = DataCategory::find($replacementCategoryId);
        if (! $old || ! $new) {
            return ['status' => 'failure', 'message' => 'Selected category was not found.'];
        }

        return DB::transaction(function () use ($old, $new) {
            $movedSubcategories = DataSubCategory::query()
                ->where('data_category_id', $old->id)
                ->update(['data_category_id' => $new->id]);

            $movedRecords = DataRecord::query()
                ->where('data_category_id', $old->id)
                ->count();
            if ($movedRecords > 0) {
                DataRecord::query()
                    ->where('data_category_id', $old->id)
                    ->update(['data_category_id' => $new->id]);
            }

            $remappedLinkedSubCategories = 0;
            $linkedPublicationCategoryIds = DB::table('data_category_publication_category')
                ->where('data_category_id', $old->id)
                ->pluck('publication_category_id');
            foreach ($linkedPublicationCategoryIds as $publicationCategoryId) {
                DB::table('data_category_publication_category')->updateOrInsert(
                    [
                        'data_category_id' => (int) $new->id,
                        'publication_category_id' => (int) $publicationCategoryId,
                    ],
                    [
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
                $remappedLinkedSubCategories++;
            }
            DB::table('data_category_publication_category')
                ->where('data_category_id', $old->id)
                ->delete();

            $old->delete();

            return [
                'status' => 'success',
                'message' => 'Category deleted and data mapped successfully.',
                'data' => [
                    'moved_subcategories' => (int) $movedSubcategories,
                    'moved_records' => (int) $movedRecords,
                    'remapped_linked_subcategories' => (int) $remappedLinkedSubCategories,
                    'deleted_category_id' => (int) $old->id,
                    'replacement_category_id' => (int) $new->id,
                ],
            ];
        });
    }


    public function save_category(Request $request){
        $record = new DataCategory();

        $record->category_name    = $request->name;
        $record->url_path         = $request->url;
        $record->slug             = Str::slug($request->title);
        $record->show_on_menu     = $request->show_menu;

        $saved = $record->save();
        if ($saved) {
            $publicationCategoryIds = PublicationCategory::query()
                ->whereNull('parent_id')
                ->pluck('id');
            foreach ($publicationCategoryIds as $publicationCategoryId) {
                DB::table('data_category_publication_category')->updateOrInsert(
                    [
                        'data_category_id' => $record->id,
                        'publication_category_id' => (int) $publicationCategoryId,
                    ],
                    [
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }

        return $saved;
    }

    public function save_subcategory(Request $request){

        $record = new DataSubCategory();

        $record->sub_catgeory_name   = $request->name;
        $record->data_category_id         = $request->category_id;

        return $record->save();
    }

}