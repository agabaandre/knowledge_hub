<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\CoursesRepository;
use App\Repositories\SettingsRepository;
use App\Support\AiConfig;
use App\Support\FrappeConfig;
use App\Support\MoodleConfig;
use App\Support\OpenEdxConfig;
use App\Services\SitemapService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class AdminCoursesController extends Controller
{
    private $coursesRepository;

    private SettingsRepository $settingsRepository;

    public function __construct(CoursesRepository $coursesRepository, SettingsRepository $settingsRepository)
    {
        $this->coursesRepository = $coursesRepository;
        $this->settingsRepository = $settingsRepository;
    }

    public function index(Request $request){

        $data['courses'] = $this->coursesRepository->get($request);
        $data['categories'] = $this->coursesRepository->categories();
        $data['search']    = (Object) $request->all();
        return view('admin.courses.index',$data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'shortname' => 'required|string|max:255',
            'cover_image' => 'nullable|string',
           // 'category_id' => 'required|integer|exists:course_categories,id',
            'summary' => 'nullable|string',
            'provider' => 'nullable|string',
            'content' => 'nullable|string',
            'course_url' => 'required|string',
            'is_moodle' => 'boolean',
            'is_active' => 'boolean',
        ]);
        $validated['is_moodle'] = $request->has('is_moodle') ? (bool)$request->is_moodle : false;
        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : true;
        $this->coursesRepository->create($validated);
        return redirect()->back()->with('success', 'Course created successfully.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'fullname' => 'sometimes|required|string|max:255',
            'shortname' => 'sometimes|required|string|max:255',
            'cover_image' => 'nullable|string',
          //  'category_id' => 'sometimes|required|integer|exists:course_categories,id',
            'summary' => 'nullable|string',
            'provider' => 'nullable|string',
            'content' => 'nullable|string',
            'course_url' => 'sometimes|required|string',
            'is_moodle' => 'boolean',
            'is_active' => 'boolean',
        ]);
        $this->coursesRepository->update($id, $validated);
        return redirect()->back()->with('success', 'Course updated successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);
        try {
            $rows = Excel::toArray([], $request->file('file'))[0];

       
            $courses = [];
            if ($request->has('has_header') && $request->has_header) {
                $header = array_shift($rows);
                foreach ($rows as $row) {
                    $courses[] = array_combine($header, $row);
                }
            } else {
                // Default column order if no header: fullname, shortname, cover_image, category_id, summary, provider, content, course_url, is_moodle, is_active
                foreach ($rows as $row) {
                    $courses[] = [
                        'fullname'    => $row[0] ?? null,
                        'shortname'   => $row[1] ?? null,
                        'cover_image' => $row[2] ?? null,
                        'category_id' => $row[3] ?? null,
                        'summary'     => $row[4] ?? null,
                        'provider'    => $row[5] ?? null,
                        'content'     => str_replace('&quot;', '', $row[6] ?? null),
                        'course_url'  => $row[7] ?? null,
                        'is_moodle'   => $row[8] ?? null,
                        'is_active'   => $row[9] ?? null,
                    ];
                }
            }
            $this->coursesRepository->import($courses);
            return redirect()->back()->with('success', 'Courses imported successfully.');
        } catch (\Exception $e) {
            \Log::error('Course import failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        $deleted = $this->coursesRepository->delete($request->id);
        
        if ($request->ajax()) {
            return response()->json([
                'status' => $deleted ? 'success' : 'failure',
                'message' => $deleted ? 'Course deleted successfully' : 'Course not found or deletion failed'
            ]);
        }
        
        return redirect()->back()->with([
            'message' => $deleted ? 'Course deleted successfully' : 'Delete failed',
            'status' => $deleted ? 'success' : 'failure'
        ]);
    }

    public function integrations()
    {
        return view('admin.courses.integrations', $this->learningIntegrationFields());
    }

    public function saveIntegrations(Request $request)
    {
        $saved = $this->settingsRepository->saveLearningIntegrations($request);

        if ($saved) {
            return redirect()
                ->route('admin.courses.integrations')
                ->with('message', __('admin_nav.learning_integrations_saved_success'))
                ->with('status', 'success');
        }

        return redirect()
            ->route('admin.courses.integrations')
            ->with('message', __('admin_nav.learning_integrations_saved_failure'))
            ->with('status', 'failure');
    }

    public function aiConfig()
    {
        return view('admin.courses.ai_config', $this->aiConfigFields());
    }

    public function saveAiConfig(Request $request)
    {
        $saved = $this->settingsRepository->saveAiIntegrations($request);

        if ($saved) {
            return redirect()
                ->route('admin.courses.ai-config')
                ->with('message', __('admin_nav.ai_config_saved_success'))
                ->with('status', 'success');
        }

        return redirect()
            ->route('admin.courses.ai-config')
            ->with('message', __('admin_nav.ai_config_saved_failure'))
            ->with('status', 'failure');
    }

    public function sitemap(SitemapService $sitemapService)
    {
        return view('admin.courses.sitemap', [
            'sitemapPage' => $sitemapService->adminSummary(),
        ]);
    }

    public function generateSitemap()
    {
        try {
            Artisan::call('sitemap:generate', ['--warm-cache' => true]);

            return redirect()
                ->route('admin.courses.sitemap')
                ->with('message', __('admin_nav.sitemap_generated_success'))
                ->with('status', 'success');
        } catch (\Throwable $e) {
            \Log::error('admin.sitemap.generate_failed', [
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->route('admin.courses.sitemap')
                ->with('message', __('admin_nav.sitemap_generated_failure', ['error' => $e->getMessage()]))
                ->with('status', 'failure');
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function learningIntegrationFields(): array
    {
        return [
            'moodleFields' => Schema::hasColumn('setting', 'moodle_api_url')
                ? MoodleConfig::fieldsForAdmin()
                : [],
            'frappeFields' => Schema::hasColumn('setting', 'frappe_base_url')
                ? FrappeConfig::fieldsForAdmin()
                : [],
            'openEdxFields' => Schema::hasColumn('setting', 'openedx_lms_url')
                ? OpenEdxConfig::fieldsForAdmin()
                : [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function aiConfigFields(): array
    {
        if (! Schema::hasColumn('setting', 'ai_openai_api_key')) {
            return ['aiPage' => null];
        }

        return [
            'aiPage' => AiConfig::adminPageData(),
        ];
    }
}
