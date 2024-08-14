<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class MoodleController extends Controller
{
    private $baseUrl;
    private $token;

    public function __construct()
    {
        $this->baseUrl = 'https://khub.africacdc.org/elearning/webservice/rest/server.php';
        $this->token = env('MOODLE_API_TOKEN'); // Replace with your Moodle API token
    }

    public function categories()
    {
        $client = new Client();
        $response = $client->request('GET', $this->baseUrl, [
            'query' => [
                'wstoken' => $this->token,
                'wsfunction' => 'core_course_get_categories',
                'moodlewsrestformat' => 'json'
            ]
        ]);

        return response()->json(json_decode($response->getBody()));
    }

    public function courses()
    {
        $client = new Client();
        $response = $client->request('GET', $this->baseUrl, [
            'query' => [
                'wstoken' => $this->token,
                'wsfunction' => 'core_course_get_courses',
                'moodlewsrestformat' => 'json'
            ]
        ]);

        return response()->json(json_decode($response->getBody()));
    }

    public function enrollments($courseId)
    {
        $client = new Client();
        $response = $client->request('GET', $this->baseUrl, [
            'query' => [
                'wstoken' => $this->token,
                'wsfunction' => 'core_enrol_get_enrolled_users',
                'moodlewsrestformat' => 'json',
                'courseid' => $courseId
            ]
        ]);

        return response()->json(json_decode($response->getBody()));
    }
}