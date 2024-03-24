<?php

namespace App\Http\Controllers\Api;
use OpenApi\Attributes as OA;
use App\Http\Controllers\Controller;

#[
    OA\Server(url: 'http://localhost/knowhub', description: "Local Development server"),
    OA\Server(url: 'https://khub.africacdc.org/', description: "Production server"),
]

//OA\SecurityScheme( securityScheme: 'jwt', type: "http", name: "Authorization", in: "header", scheme: "bearer",bearerFormat: "JWT"),
class ApiController extends Controller{


}