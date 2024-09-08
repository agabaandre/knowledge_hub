<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Publication;
use Illuminate\Http\Request;
use App\Services\AIModel;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    private $aiModel;

    // public function __construct(AIModel $ai_model)
    // {
    //     $this->aiModel = $ai_model;
    // }


    public function summarise(Request $request){

        

        $this->aiModel = app('chatgpt');
  
        if(intval($request->type) !== 1):

           
            $resource = Publication::find($request->resource_id);

            if(strpos($resource->publication,'.pdf') >-1){

                //Use Chat PDF if we have a pdf
                $this->aiModel = app('chatpdf');
                $response = $this->aiModel->summarize($resource,$request->language);
            }
            else{
                
                //No PDF, Use Chat GPT

                $prompt = "Use summary language: $request->language,title: $resource->title,  
                body: $resource->description  ,
                attached_content: ". truncate(pdfToText($resource->publication),100000).", 
                comments: ".json_encode($resource->comments->toArray());

                $response = $this->aiModel->summarize($prompt);
            }

        else:

            $resource = Forum::find($request->resource_id);
            $prompt = "summary language: $request->language,forum title: $resource->forum_title,
             forum content: $resource->forum_description ,  
             forum comments: ".json_encode($resource->comments->toArray());
             $response = $this->aiModel->summarize($prompt);

        endif;

        
        Log::info("RESPONSE: ".json_encode($response));

        if(isset($response->content))
            return ['content'=>$response->content];

        if(isset($response->message))
            return ['content'=>$response->message];

        if(isset($response->choices))
            return ['content'=>$response->choices[0]->message->content];

        return ['content'=>"<div class='alert alert-danger'><p>Failure: ".$response->error->message ?? 'Error'.'</p></div>'];
    }


    public function compare(Request $request){

        $this->aiModel = app('chatgpt');
  
        $pub = Publication::find($request->resource_id);
        $pub2 = Publication::find($request->other_resource_id);

        $response = $this->aiModel->compare($pub->description,$pub2->description);
        
        return ['content'=>$response->choices[0]->message->content];
    }

  
}
