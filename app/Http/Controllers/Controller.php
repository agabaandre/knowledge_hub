<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *    title="AFRICA CDC KnowledgeHub API",
 *    version="1.0.0",
 * )
 *
 * @OA\Server(
 *     url=L5_OPENAPI_SERVER_URL,
 *     description="Current deployment (set APP_URL in .env)"
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
