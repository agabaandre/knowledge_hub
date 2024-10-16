<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Repositories\CoursesRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Api\ApiController;

class CoursesApiController extends ApiController
{
    protected $coursesRepository;

    public function __construct(CoursesRepository $coursesRepository)
    {
        $this->coursesRepository = $coursesRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/courses",
     *     operationId="getCoursesList",
     *     tags={"Courses"},
     *     summary="Get list of courses",
     *     description="Returns list of courses",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function index(Request $request)
    {
        $courses = $this->coursesRepository->get($request);
        return response()->json($courses);
    }

    /**
     * @OA\Post(
     *     path="/api/courses",
     *     operationId="storeCourse",
     *     tags={"Courses"},
     *     summary="Store new course",
     *     description="Returns course data",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="fullname", type="string"),
     *             @OA\Property(property="category_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'fullname' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:course_categories,id',
        ]);

        $course = Course::create($validatedData);
        return response()->json($course, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/courses/{id}",
     *     operationId="getCourseById",
     *     tags={"Courses"},
     *     summary="Get course information",
     *     description="Returns course data",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Course not found"
     *     )
     * )
     */
    public function show($id)
    {
        $course = Course::findOrFail($id);
        return response()->json($course);
    }

    /**
     * @OA\Put(
     *     path="/api/courses/{id}",
     *     operationId="updateCourse",
     *     tags={"Courses"},
     *     summary="Update existing course",
     *     description="Returns updated course data",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="fullname", type="string"),
     *             @OA\Property(property="category_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Course not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'fullname' => 'sometimes|required|string|max:255',
            'category_id' => 'sometimes|required|integer|exists:course_categories,id',
        ]);

        $course = Course::findOrFail($id);
        $course->update($validatedData);
        return response()->json($course);
    }

    /**
     * @OA\Delete(
     *     path="/api/courses/{id}",
     *     operationId="deleteCourse",
     *     tags={"Courses"},
     *     summary="Delete course",
     *     description="Deletes a record and returns no content",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Course not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        return response()->json(null, 204);
    }
}