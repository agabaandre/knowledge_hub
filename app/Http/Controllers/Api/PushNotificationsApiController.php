<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\PushNotificationsRepository;

class PushNotificationsApiController extends Controller
{
    protected $pushNotificationsRepo;

    public function __construct(PushNotificationsRepository $pushNotificationsRepo)
    {
        $this->pushNotificationsRepo = $pushNotificationsRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/push-notifications",
     *     operationId="getPushNotificationsList",
     *     tags={"PushNotifications"},
     *     summary="Get list of push notifications",
     *     description="Returns list of push notifications",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function index()
    {
       
        $data   = $this->pushNotificationsRepo->getAll()->toArray();
        $data["status"]  = 200;
        $data["message"] = "Push notifications retrieved successfully";

        return response()->json($data, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/push-notifications",
     *     operationId="storePushNotification",
     *     tags={"PushNotifications"},
     *     summary="Store new push notification",
     *     description="Returns push notification data",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent()
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
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'is_topic' => 'required|boolean',
            'user_id' => 'nullable|string|max:255',
        ]);

        $pushNotification = $this->pushNotificationsRepo->create($request->all());

        return response()->json([
            "status" => 201,
            "message" => "Push notification created successfully",
            "data" => $pushNotification
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/push-notifications/{id}",
     *     operationId="getPushNotificationById",
     *     tags={"PushNotifications"},
     *     summary="Get push notification information",
     *     description="Returns push notification data",
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
     *         description="Push notification not found"
     *     )
     * )
     */
    public function show($id)
    {
        $pushNotification = $this->pushNotificationsRepo->find($id);

        if (!$pushNotification) {
            return response()->json([
                "status" => 404,
                "message" => "Push notification not found",
                "data" => null
            ], 404);
        }

        return response()->json([
            "status" => 200,
            "message" => "Push notification retrieved successfully",
            "data" => $pushNotification
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/push-notifications/{id}",
     *     operationId="updatePushNotification",
     *     tags={"PushNotifications"},
     *     summary="Update existing push notification",
     *     description="Returns updated push notification data",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Push notification not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $pushNotification = $this->pushNotificationsRepo->find($id);

        if (!$pushNotification) {
            return response()->json([
                "status" => 404,
                "message" => "Push notification not found",
                "data" => null
            ], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'is_topic' => 'required|boolean',
            'user_id' => 'nullable|string|max:255',
        ]);

        $this->pushNotificationsRepo->update($pushNotification, $request->all());

        return response()->json([
            "status" => 200,
            "message" => "Push notification updated successfully",
            "data" => $pushNotification
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/push-notifications/{id}",
     *     operationId="deletePushNotification",
     *     tags={"PushNotifications"},
     *     summary="Delete push notification",
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
     *         description="Push notification not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $pushNotification = $this->pushNotificationsRepo->find($id);

        if (!$pushNotification) {
            return response()->json([
                "status" => 404,
                "message" => "Push notification not found",
                "data" => null
            ], 404);
        }

        $this->pushNotificationsRepo->delete($pushNotification);

        return response()->json([
            "status" => 204,
            "message" => "Push notification deleted successfully",
            "data" => null
        ], 204);
    }

    /**
     * @OA\Get(
     *     path="/api/push-notifications/user",
     *     operationId="getPushNotificationsByUser",
     *     tags={"PushNotifications"},
     *     security={{"bearer_token":{}}},
     *     summary="Get push notifications by user",
     *     description="Returns list of push notifications for a specific user",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function getByUser()
    {
        $userId = auth()->user()->id ?? 0;
        $notifications = $this->pushNotificationsRepo->getByUser($userId);
       
        
        $data            = $notifications->toArray();
        $data["status"]  = 200;
        $data["message"] = "Push notifications retrieved successfully";
        
        if ($notifications->isEmpty()) {

            $data["status"]  = 404;
            $data["message"] = "No notifications found for this user";
            $data["data"]    = $notifications->toArray();
            
            return response()->json($data, 404);
        }

        return response()->json($data, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/push-notifications/mark-as-read",
     *     operationId="markNotificationsAsRead",
     *     tags={"PushNotifications"},
     *     summary="Mark notifications as read",
     *     description="Marks specified notifications as read",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="ids", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notifications marked as read successfully",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     */
    public function markAsRead(Request $request)
    {
        $request->validate([
            'ids' => 'required',
            'ids.*' => 'integer|exists:push_notifications,id',
        ]);

        $userId = auth()->user()->id ?? 0;
       
        if($userId == 0){
            return response()->json([
                "status" => 401,
                "message" => "Unauthenticated",
                "count" => 0
            ], 401);
        }

        $this->pushNotificationsRepo->markAsRead($userId,$request->ids);

        return response()->json([
            "status" => 200,
            "message" => "Notifications marked as read successfully"
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/push-notifications/unread-count",
     *     operationId="getUnreadNotificationsCount",
     *     tags={"PushNotifications"},
     *     summary="Get count of unread notifications",
     *     description="Returns the count of unread notifications for the authenticated user",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function getUnreadCount()
    {

        $userId = auth()->user()->id ?? 0;
       
        if($userId == 0){
            return response()->json([
                "status" => 401,
                "message" => "Unauthenticated",
                "count" => 0
            ], 401);
        }

        $count = $this->pushNotificationsRepo->getUnreadCount($userId);

        return response()->json([
            "status" => 200,
            "message" => "Unread notifications count retrieved successfully",
            "count" => $count
        ], 200);
    }
}
