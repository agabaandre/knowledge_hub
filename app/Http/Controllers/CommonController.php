<?php

namespace App\Http\Controllers;

use App\Repositories\FaqsRepository;
use Illuminate\Http\Request;
class CommonController extends Controller
{
    private $faqsRepo;

    public function __construct( FaqsRepository $faqsRepo)
    {
        $this->faqsRepo       = $faqsRepo;
    }

    public function privacy(Request $request){

        $data['policy'] = file_get_contents(storage_link('uploads/privacy/privacy_policy.md'));
        return view('privacy.index',$data);
    }

}
