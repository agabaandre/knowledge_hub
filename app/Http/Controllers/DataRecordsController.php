<?php

namespace App\Http\Controllers;

use App\Repositories\AssetsRepository;
use App\Repositories\DataRecordsRepository;
use Illuminate\Http\Request;
class DataRecordsController extends Controller
{
    private $dataRecordsRepo;

    public function __construct( DataRecordsRepository $dataRecordsRepo)
    {
        $this->dataRecordsRepo       = $dataRecordsRepo;
    }

    public function index(Request $request){

        $data['records'] = $this->dataRecordsRepo->get($request);
        return view('datarecords.index',$data);
    }

    public function details(Request $request){
        $data['record'] = $this->dataRecordsRepo->find($request->id);
        return view('datarecords.details',$data);
    }

}
