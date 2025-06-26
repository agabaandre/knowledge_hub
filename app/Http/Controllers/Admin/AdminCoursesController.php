<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\AdminUnitsRepository;
use App\Http\Controllers\Controller;
use App\Repositories\CoursesRepository;
use Maatwebsite\Excel\Facades\Excel;

class AdminCoursesController extends Controller
{
    private $coursesRepository;

    public function __construct(CoursesRepository $coursesRepository)
    {
        $this->coursesRepository = $coursesRepository;
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
}
