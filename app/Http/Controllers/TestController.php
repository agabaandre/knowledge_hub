<?php

namespace App\Http\Controllers;

use App\Jobs\SendMailJob;
use Illuminate\Http\Request;
use App\Repositories\IndicatorsRepository;
use App\Repositories\UsersRepository;


class TestController extends Controller
{
    private $repo;

    public function __construct(UsersRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request){

        $body = view('emails.email_verification',['token'=>'87487487'])->render();
        $request['email']   = 'henricsanyu@gmail.com';
        $request['subject'] = 'henricsanyu@gmail.com';
        $request['body'] = $body;
        
        SendMailJob::dispatch($request);
        
    }

  

    
}
