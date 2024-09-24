<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\Country;

class MembersApiController extends ApiController
{
  
    public function __construct(){

    }

    /**
        * @OA\Get(
        * path="/api/members",
        * operationId="List Member States",
        * tags={"List  Member States"},
        * summary="List  Member States",
        * description="Returns a list of all member statess",
        *      @OA\Response(
        *          response=200,
        *          description="Successful",
        *          @OA\JsonContent()
        *       )
        * )
        */
        public function member_states()
        {
            $countries = Country::where('national','national')->get()->toArray() ?? [];
            return [
                "status" => 200,
                "data" => $countries
            ];
        }

}