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
    public const COURSES_INFINITE_ROWS = 6;

    private $courseRepo;

    public function __construct(CoursesRepository $courseRepo)
    {
        $this->courseRepo = $courseRepo;
    }

    public function index(Request $request)
    {
        $this->prepareCoursesListingRequest($request);
        $data['courses'] = $this->courseRepo->get($request);
        $data['canFetchCourses'] = $request->user() && $request->user()->can('view_forumns');
        $data['coursesInfiniteScroll'] = $this->coursesInfiniteScrollEnabled();

        return view('courses.index', $data);
    }

    public function coursesPage(Request $request)
    {
        if (! $this->coursesInfiniteScrollEnabled()) {
            return response()->json(['ok' => false, 'error' => 'infinite_scroll_disabled'], 403);
        }

        $this->prepareCoursesListingRequest($request);
        $request->merge(['rows' => self::COURSES_INFINITE_ROWS]);
        $courses = $this->courseRepo->get($request);

        $page = (int) $courses->currentPage();
        $perPage = (int) $courses->perPage();
        $listOffset = max(0, ($page - 1) * $perPage);
        $loadedCount = min($courses->total(), $listOffset + $courses->count());

        return response()->json([
            'ok' => true,
            'html' => view('courses.partials.course_list_items', ['courses' => $courses])->render(),
            'current_page' => $page,
            'last_page' => (int) $courses->lastPage(),
            'has_more' => $courses->hasMorePages(),
            'total' => (int) $courses->total(),
            'loaded_count' => $loadedCount,
        ]);
    }

    protected function prepareCoursesListingRequest(Request $request): void
    {
        if ($this->coursesInfiniteScrollEnabled()) {
            $request->merge([
                'page' => max(1, (int) $request->input('page', 1)),
                'rows' => self::COURSES_INFINITE_ROWS,
            ]);
        }
    }

    protected function coursesInfiniteScrollEnabled(): bool
    {
        return (settings()->courses_pagination_mode ?? 'infinite_scroll') === 'infinite_scroll';
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
