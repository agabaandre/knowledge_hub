<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    private $aiModel;

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
  
            $resource  = Publication::find($request->resource_id);
            $resource2 = Publication::find($request->other_resource_id);

            if(strpos($resource->publication,'.pdf') >-1 && strpos($resource2->publication,'.pdf') >-1){

                //Use Chat PDF if we have a pdf
                $this->aiModel = app('chatpdf');
                $response = $this->aiModel->compare($resource,$resource);
            }
            else{
                
                //No PDF, Use Chat GPT

                $prompt = "First One: title: $resource->title,  
                body: $resource->description  ,
                attached_content: ". truncate(pdfToText($resource->publication),100000).", 
                comments: ".json_encode($resource->comments->toArray());


                $prompt2 = "Second One: title: $resource2->title,  
                body: $resource2->description  ,
                attached_content: ". truncate(pdfToText($resource2->publication),100000).", 
                comments: ".json_encode($resource2->comments->toArray());

                $response = $this->aiModel->compare($prompt,$prompt2);
            }

        
        Log::info("RESPONSE: ".json_encode($response));

        if(isset($response->content))
            return ['content'=>$response->content];

        if(isset($response->message))
            return ['content'=>$response->message];

        if(isset($response->choices))
            return ['content'=>$response->choices[0]->message->content];

        return ['content'=>"<div class='alert alert-danger'><p>Failure: ".$response->error->message ?? 'Error'.'</p></div>'];
    }

  
}
