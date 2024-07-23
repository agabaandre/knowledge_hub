<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Publication;
use Illuminate\Http\Request;
use App\Services\AIModel;

class AIController extends Controller
{
    private $aiModel;

    public function __construct(AIModel $ai_model)
    {
        $this->aiModel = $ai_model;
    }


    public function summarise(Request $request){
  
        if(intval($request->type) !== 1):
            $resource = Publication::find($request->resource_id);
            $prompt = "title: $resource->title,  
            body: $resource->description  ,
            attached_content: ". truncate(pdfToText($resource->publication),10000).", 
            comments: ".json_encode($resource->comments->toArray());
        else:
            $resource = Forum::find($request->resource_id);
            $prompt = "forum title: $resource->forum_title,
             forum content: $resource->forum_description ,  
             forum comments: ".json_encode($resource->comments->toArray());
        endif;

        $response = $this->aiModel->summarize($prompt);

        if(isset($response->choices))
            return ['content'=>$response->choices[0]->message->content];

        return ['content'=>"<div class='alert alert-danger'><p>Failure: ".$response->error->message.'</p></div>'];
    }


    public function compare(Request $request){
  
        $pub = Publication::find($request->resource_id);
        $pub2 = Publication::find($request->other_resource_id);

        $response = $this->aiModel->compare($pub->description,$pub2->description);
        
        return ['content'=>$response->choices[0]->message->content];
    }

  
}
