<?php

namespace App\Http\Controllers;

use App\Repositories\FactsRepository;
use Illuminate\Http\Request;
class FactsController extends Controller
{
    private $factsRepo;

    public function __construct( FactsRepository $factsRepo)
    {
        $this->factsRepo       = $factsRepo;
    }

    public function details(Request $request){

        $data['fact'] = $this->factsRepo->find($request->ref);
        return view('facts.details',$data);
    }

}
