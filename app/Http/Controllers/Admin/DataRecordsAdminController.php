<?php

namespace App\Http\Controllers\Admin;

use App\Models\DataSubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\DataRecordsRepository;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class DataRecordsAdminController extends Controller
{
    private $dataRecordsRepo;

    public function __construct(DataRecordsRepository $dataRecordsRepo)
    {
        $this->dataRecordsRepo = $dataRecordsRepo;
    }



    public function index(Request $request){

        $data['records'] = $this->dataRecordsRepo->get($request);
        $data['search']       = (Object) $request->all();
        $data['countries'] = $this->dataRecordsRepo->get_json_countries();
        $data['categories'] = $this->dataRecordsRepo->get_json_categories();
        return view('admin.datarecords.index',$data);
    }

    
    public function create(Request $request){

        $data['record'] = null;
        return view('admin.datarecords.create',$data);
    }

    public function edit(Request $request){

        $data['record']  =  $this->dataRecordsRepo->find($request->id);
        return view('admin.datarecords.create',$data);
    }

    public function store(Request $request){

        $saved = $this->dataRecordsRepo->save($request);


        if($saved):
            $data = ['message'=>'Record saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        if($request->ajax()){
            return response($data,200);
        }

        return back()->with($data);
    }


    public function destroy(Request $request){
        if (!auth()->user() || !auth()->user()->can('delete_meta_data')) {
            return response(['status'=>'failure','message'=>'Unauthorized'], 403);
        }
        return $this->dataRecordsRepo->delete($request->id);
    }

    public function categories(Request $request){

        $data['categories'] = $this->dataRecordsRepo->get_categories($request);
        $data['allCategoriesForMapping'] = $this->dataRecordsRepo->allCategoriesForMapping();
        $data['search']       = (Object) $request->all();
        $data['categoryPermissions'] = $this->categoryPermissionNames();
        return view('admin.datarecords.categories',$data);
    }


    public function subcategories(Request $request){

        $data['subcategories'] = $this->dataRecordsRepo->get_subcategories($request);
        $data['categories']    = $this->dataRecordsRepo->get_categories($request);
        $data['search']        = (Object) $request->all();
        return view('admin.datarecords.subcategories',$data);
    }

    public function delete_category(Request $request){
        if (!auth()->user() || !auth()->user()->can('delete_publication_metadata')) {
            return response(['status'=>'failure','message'=>'Unauthorized'], 403);
        }
        $request->validate([
            'id' => 'required|integer|exists:data_categories,id',
            'replacement_category_id' => 'required|integer|exists:data_categories,id|different:id',
        ]);

        $result = $this->dataRecordsRepo->deleteCategoryWithMapping((int) $request->id, (int) $request->replacement_category_id);
        $statusCode = ($result['status'] ?? 'failure') === 'success' ? 200 : 422;

        return response()->json($result, $statusCode);
    }

    public function save_category(Request $request){

        $saved = $this->dataRecordsRepo->save_category($request);

        if($saved):
            $data = ['message'=>'Record saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        if($request->ajax()){
            return response($data,200);
        }
        return back()->with($data);
    }

    public function update_category(Request $request)
    {
        $saved = $this->dataRecordsRepo->update_category($request);

        if ($saved) {
            $data = ['message' => 'Category updated successfully', 'status' => 'success', 'data' => $saved];
        } else {
            $data = ['message' => 'Operation failed, try again', 'status' => 'failure', 'data' => $saved];
        }

        if ($request->ajax()) {
            return response($data, 200);
        }

        return back()->with($data);
    }

    public function save_subcategory(Request $request){

        $saved = $this->dataRecordsRepo->save_subcategory($request);

        if($saved):
            $data = ['message'=>'Record saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        if($request->ajax()){
            return response($data,200);
        }
        return back()->with($data);
    }


    public function getSubcategories(Request $request)
    {
        $category_id = $request->input('category_id');
        
        // Fetch subcategories based on the selected category ID (A–Z for admin + modals)
        $subcategories = DataSubCategory::query()
            ->where('data_category_id', $category_id)
            ->orderBy('sub_catgeory_name', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Return subcategories as JSON response
        return response()->json($subcategories);
    }

    private function categoryPermissionNames()
    {
        try {
            if (! Schema::hasTable('permissions')) {
                return collect();
            }

            return Permission::query()->orderBy('name')->pluck('name');
        } catch (\Throwable $e) {
            return collect();
        }
    }


}
