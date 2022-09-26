<?php

function preparePostFields($array)
{
    $params = array();

    foreach ($array as $key => $value) {
        $params[] = $key . '=' . urlencode($value);
    }

    return implode('&', $params);
}

function send_sms($to, $msg)
{

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.africastalking.com/version1/messaging',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => preparePostFields(array(
            'username' => "CASHAWo",
            'to' => "256" . substr($to, -9),
            'message' => $msg,
        )),
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'apiKey: '
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
}


// send_sms($to="256702544870", $msg="Hello World");