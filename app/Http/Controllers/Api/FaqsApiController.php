<?php

namespace App\Http\Controllers\Api;

use App\Repositories\FaqsRepository;
use Illuminate\Http\Request;

class FaqsApiController extends ApiController
{
    public function __construct(private FaqsRepository $faqsRepo)
    {
    }

    public function index(Request $request)
    {
        $request->merge([
            'rows' => $request->input('page_size', $request->input('rows', 20)),
        ]);
        $paginator = $this->faqsRepo->get($request);
        $data = $paginator->toArray();
        $data['status'] = 200;
        unset($data['links'], $data['last_page_url'], $data['next_page_url'], $data['path'], $data['first_page_url'], $data['prev_page_url']);

        return response()->json($data);
    }
}
