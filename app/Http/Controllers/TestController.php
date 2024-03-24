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

            $client  = ApiClient::first();
            $credentials =['api_key'=>$client->api_key,'api_secret'=>base64_encode($client->api_secret)];

            return response(json_encode($credentials));
    }

  

    
}
