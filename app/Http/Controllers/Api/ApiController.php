<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * Base class for JSON API controllers. Global OpenAPI metadata (`@OA\Info`, `@OA\Server`, `@OA\Tag`)
 * lives on {@see \App\Http\Controllers\Controller}. The server URL uses `L5_OPENAPI_SERVER_URL`
 * (defaults from `APP_URL` in `.env` via `config/l5-swagger.php`).
 */
class ApiController extends Controller
{
}
