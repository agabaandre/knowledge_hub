<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\DashboardsRepository;
class DashboardsController extends Controller
{
    
    private $dashBoardsRepo;

    public function __construct(DashboardsRepository $dashRepo) {
        
        $this->dashBoardsRepo         = $dashRepo;
        
    }
    
    public function index(Request $request){

        $data['dashboards'] = $this->dashBoardsRepo->member_states($request);
        return view('countries.index',$data);
    }
}
