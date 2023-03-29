<?php

namespace App\Http\Controllers;

use App\Repositories\AssetsRepository;
use App\Repositories\FaqsRepository;
use Illuminate\Http\Request;
class FaqsController extends Controller
{
    private $faqsRepo;

    public function __construct( FaqsRepository $faqsRepo)
    {
        $this->faqsRepo       = $faqsRepo;
    }

    public function index(Request $request){

        $data['faqs'] = $this->faqsRepo->get($request);
        return view('faqs.index',$data);
    }

}
