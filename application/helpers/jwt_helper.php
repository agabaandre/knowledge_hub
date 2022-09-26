<?php

use App\Models\UserModel;
use Config\Services;
use Firebase\JWT\JWT;

function getSecretKey(){
    return env('JWT_SECRET_KEY');
} 

function getJWTFromRequest($authenticationHeader)
{
    if (is_null($authenticationHeader)) { //JWT is absent
        throw new Exception('Missing or invalid request authentication');
    }
    //JWT is sent from client in the format Bearer XXXXXXXXX
    return explode(' ', $authenticationHeader)[1];
}

function validateJWTFromRequest($encodedToken)
{
    $key = getSecretKey();
    $decodedToken = JWT::decode($encodedToken, $key, ['HS256']);
  
    if (is_null( CI_INSTANCE()->getUserByEmail($decodedToken->email) ) { //Invalid user
        throw new Exception('Invalid request authentication');
    }
}

function getSignedJWTForUser($email)
{
    $issuedAtTime = time();
    $tokenTimeToLive = env('JWT_TIME_TO_LIVE');
    $tokenExpiration = $issuedAtTime + $tokenTimeToLive;
    $payload = [
        'email' => $email,
        'iat' => $issuedAtTime,
        'exp' => $tokenExpiration,
    ];

    $jwt = JWT::encode($payload, getSecretKey());
    return $jwt;
}

