<?php

namespace App\Http\Controllers;

use App\Jobs\SendMailJob;
use App\Models\ApiClient;
use App\Models\Publication;
use Illuminate\Http\Request;
use App\Repositories\UsersRepository;
use App\Services\AIModel;

class TestController extends Controller
{
    private $repo;

    public function __construct(UsersRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request){

        $body            = view('emails.email_verification',['token'=>'B65AjtMKwG'])->render();
        $data['email']   = 'agabaandre@gmail.com';
        $data['subject'] = 'Grretings from Us';
        $data['body']    = $body;
        SendMailJob::dispatch($data);

            // $client  = ApiClient::first();
            // $credentials =['api_key'=>$client->api_key,'api_secret'=>base64_encode($client->api_secret)];

            // return response(json_encode($credentials));
    }

    public function chat(AIModel $aiModel){

        dd(pdfToText("http://library.health.go.ug/sites/default/files/resources/Selected%20Practice%20recommendation%20for%20the%20preventions%20of%20Contraceptive%20use.pdf"));
  
        $pub = Publication::find(24);
        $pub2 = Publication::find(23);

        $response = $aiModel->summarize($pub->description);
        dd($response->choices[0]->message->content);

        //$response = $aiModel->compare($pub->description,$pub2->description);
        //dd($response->choices[0]->message->content);

    }

  

    
}
