<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCourseSyncJob;
use App\Models\CourseSyncRun;
use App\Repositories\CoursesRepository;
use App\Services\CourseSyncRunService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CoursesController extends Controller
{
    private $courseRepo;

    public function __construct(CoursesRepository $courseRepo)
    {
        $this->courseRepo = $courseRepo;
    }

    public function index(Request $request)
    {
        $data['courses'] = $this->courseRepo->get($request);
        $data['canFetchCourses'] = $request->user() && $request->user()->can('view_forumns');

        return view('courses.index', $data);
    }

    public function showDetails($id)
    {
        $course = $this->courseRepo->find($id);
        if (! $course) {
            abort(404);
        }

        if ($course->isExternalCourse() && $course->external_course_url) {
            return redirect()->away($course->external_course_url);
        }

        return view('courses.show', compact('course'));
    }

    public function startFetch(Request $request, CourseSyncRunService $runs): JsonResponse
    {
        $this->authorizeCourseFetch($request);

        $run = $runs->create($request->user()->id);
        ProcessCourseSyncJob::dispatch($run->id);

        if (config('queue.default') === 'sync') {
            $run->refresh();
            if ($run->isFinished()) {
                return response()->json([
                    'success' => true,
                    'sync' => true,
                    'run_id' => $run->id,
                    'message' => $run->message,
                    'status' => $runs->toStatusArray($run),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'run_id' => $run->id,
            'message' => 'Fetching courses from learning platforms…',
        ]);
    }

    public function fetchStatus(int $id, Request $request, CourseSyncRunService $runs): JsonResponse
    {
        $this->authorizeCourseFetch($request);

        $run = CourseSyncRun::query()->findOrFail($id);

        return response()->json($runs->toStatusArray($run));
    }

    protected function authorizeCourseFetch(Request $request): void
    {
        abort_unless($request->user(), 403);
        abort_unless($request->user()->can('view_forumns'), 403);
    }
}
