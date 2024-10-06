<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\EventsRepository;

class EventsApiController extends Controller
{
    protected $eventsRepo;

    public function __construct(EventsRepository $eventsRepo)
    {
        $this->eventsRepo = $eventsRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/events",
     *     operationId="getEventsList",
     *     tags={"Events"},
     *     summary="Get list of events",
     *     description="Returns list of events",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Event"))
     *     )
     */
    public function index()
    {
        return response()->json($this->eventsRepo->getAll(), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/events",
     *     operationId="storeEvent",
     *     tags={"Events"},
     *     summary="Store new event",
     *     description="Returns event data",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Event")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Event")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'venue' => 'required|string|max:255',
            'startdate' => 'required|date',
            'enddate' => 'required|date|after_or_equal:startdate',
            'organized_by' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'is_online' => 'required|boolean',
            'contact_person' => 'required|string|max:255',
        ]);

        $event = $this->eventsRepo->create($request->all());

        return response()->json($event, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/events/{id}",
     *     operationId="getEventById",
     *     tags={"Events"},
     *     summary="Get event information",
     *     description="Returns event data",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Event")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Event not found"
     *     )
     * )
     */
    public function show($id)
    {
        $event = $this->eventsRepo->find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        return response()->json($event, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/events/{id}",
     *     operationId="updateEvent",
     *     tags={"Events"},
     *     summary="Update existing event",
     *     description="Returns updated event data",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Event")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Event")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Event not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $event = $this->eventsRepo->find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'venue' => 'required|string|max:255',
            'startdate' => 'required|date',
            'enddate' => 'required|date|after_or_equal:startdate',
            'organized_by' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'is_online' => 'required|boolean',
            'contact_person' => 'required|string|max:255',
        ]);

        $this->eventsRepo->update($event, $request->all());

        return response()->json($event, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/events/{id}",
     *     operationId="deleteEvent",
     *     tags={"Events"},
     *     summary="Delete event",
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
     *         description="Event not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $event = $this->eventsRepo->find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $this->eventsRepo->delete($event);

        return response()->json(null, 204);
    }
}