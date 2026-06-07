<?php

namespace App\Http\Controllers;

use App\Services\FederatedContentService;
use Illuminate\Http\Request;

class FederatedBrowseController extends Controller
{
    public function __construct(private FederatedContentService $federation)
    {
    }

    public function index(Request $request)
    {
        if (! $this->federation->federationConsumerEnabled()) {
            abort(404);
        }

        $data = $this->federation->browse($request);
        $data['pageTitle'] = 'Member States knowledge hubs';
        $data['pageDescription'] = 'Browse approved resources and discussions from linked member state knowledge hubs.';

        return view('federation.browse', $data);
    }
}
