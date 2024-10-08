<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\CommsOfPracticeRepository;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Communities",
 *     description="API Endpoints for managing communities"
 * )
 */
class CommunitiesApiController extends Controller
{
    protected $commsRepo;

    public function __construct(CommsOfPracticeRepository $commsRepo)
    {
        $this->commsRepo = $commsRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/communities",
     *     operationId="getCommunitiesList",
     *     tags={"Communities"},
     *     summary="Get list of communities",
     *     description="Returns list of communities",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Community Name"),
     *                 @OA\Property(property="description", type="string", example="Description of the community")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json([
            "status" => 200,
            "message" => "Communities retrieved successfully",
            "data" => $this->commsRepo->getAllWithMembership()
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/communities",
     *     operationId="createCommunity",
     *     tags={"Communities"},
     *     summary="Create a new community",
     *     description="Creates a new community",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Community Name"),
     *             @OA\Property(property="description", type="string", example="Description of the community")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Community created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Community Name"),
     *             @OA\Property(property="description", type="string", example="Description of the community")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $community = $this->commsRepo->save($request);

        return response()->json([
            "status" => 201,
            "message" => "Community created successfully",
            "data" => $community
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/communities/{id}",
     *     operationId="getCommunityById",
     *     tags={"Communities"},
     *     summary="Get community information",
     *     description="Returns community data",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Community Name"),
     *             @OA\Property(property="description", type="string", example="Description of the community")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Community not found"
     *     )
     * )
     */
    public function show($id)
    {
        $community = $this->commsRepo->find($id);

        if (!$community) {
            return response()->json([
                "status" => 404,
                "message" => "Community not found",
                "data" => null
            ], 404);
        }

        return response()->json([
            "status" => 200,
            "message" => "Community retrieved successfully",
            "data" => $community
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/communities/{id}",
     *     operationId="updateCommunity",
     *     tags={"Communities"},
     *     summary="Update an existing community",
     *     description="Updates a community",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Community Name"),
     *             @OA\Property(property="description", type="string", example="Updated description of the community")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Community updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Updated Community Name"),
     *             @OA\Property(property="description", type="string", example="Updated description of the community")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Community not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $community = $this->commsRepo->find($id);

        if (!$community) {
            return response()->json([
                "status" => 404,
                "message" => "Community not found",
                "data" => null
            ], 404);
        }

        $community = $this->commsRepo->save($request);

        return response()->json([
            "status" => 200,
            "message" => "Community updated successfully",
            "data" => $community
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/communities/{id}",
     *     operationId="deleteCommunity",
     *     tags={"Communities"},
     *     summary="Delete a community",
     *     description="Deletes a community",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Community deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Community not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $deleted = $this->commsRepo->delete($id);

        return response()->json([
            "status" => 204,
            "message" => "Community deleted successfully",
            "data" => null
        ], 204);
    }

    /**
     * @OA\Post(
     *     path="/api/communities/{communityId}/members",
     *     operationId="addMemberToCommunity",
     *     tags={"Communities"},
     *     summary="Add a member to a community",
     *     description="Adds a member to a community",
     *     @OA\Parameter(
     *         name="communityId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Member added successfully"
     *     )
     * )
     */
    public function addMember(Request $request, $communityId)
    {
        $this->commsRepo->addMember($communityId, $request->user_id);

        return response()->json([
            "status" => 200,
            "message" => "Member added successfully",
            "data" => null
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/communities/{communityId}/members/{userId}",
     *     operationId="removeMemberFromCommunity",
     *     tags={"Communities"},
     *     summary="Remove a member from a community",
     *     description="Removes a member from a community",
     *     @OA\Parameter(
     *         name="communityId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Member removed successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Member not found"
     *     )
     * )
     */
    public function removeMember($communityId, $userId)
    {
        $removed = $this->commsRepo->removeMember($communityId, $userId);

        return response()->json([
            "status" => $removed ? 200 : 404,
            "message" => $removed ? "Member removed successfully" : "Member not found",
            "data" => null
        ], $removed ? 200 : 404);
    }
}