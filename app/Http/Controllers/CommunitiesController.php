<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\CommsOfPracticeRepository;
use Illuminate\Support\Facades\Auth;

class CommunitiesController extends Controller
{
    private $commsOfPracticeRepository;

    public function __construct(CommsOfPracticeRepository $commsOfPracticeRepository)
    {
        $this->commsOfPracticeRepository = $commsOfPracticeRepository;
    }

    public function index()
    {
        $communities = $this->commsOfPracticeRepository->get(request());
        return view('communities.index', compact('communities'));
    }

    public function join(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['redirect' => url('/login')]);
        }

        $result = $this->commsOfPracticeRepository->addMember($request->community_id, Auth::id());

        return response()->json(['status' => 'success', 'message' => 'You request has been successfully submited to the community.']);
    }

    public function leave(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['redirect' => url('/login')]);
        }

        $result = $this->commsOfPracticeRepository->removeMember($request->community_id, Auth::id());

        if ($result) {
            return response()->json(['status' => 'success', 'message' => 'You have successfully left the community.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Failed to leave the community.']);
        }
    }
}
