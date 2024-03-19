<?php

namespace App\Http\Controllers;

use App\Jobs\SendMailJob;
use App\Models\ApiClient;
use Illuminate\Http\Request;
use App\Repositories\UsersRepository;

class TestController extends Controller
{
    private $repo;

    public function __construct(UsersRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request){

        // $body            = view('emails.email_verification',['token'=>'B65AjtMKwG'])->render();
        // $data['email']   = 'henricsanyu@gmail.com';
        // $data['subject'] = 'Grretings from Us';
        // $data['body']    = $body;
        // SendMailJob::dispatch($data);

            $user  = ApiClient::first();
            $token = $user->createToken("API Client");
            return response(json_encode(['token' => $token->plainTextToken]));
    }

  

    
}
