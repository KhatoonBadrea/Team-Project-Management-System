<?php

namespace App\Http\Controllers\Api\project;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ProjectUserService;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\StorePivotRequest;
use App\Http\Requests\UpdatePivotRequest;

class ProjectUserController extends Controller
{

    use ApiResponseTrait;

    protected $projectUserService;

    public function __construct(ProjectUserService $projectUserService)
    {
        $this->projectUserService = $projectUserService;
    }
    /**
     * Display a listing of the project and user that join on it.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {

            $projects = $this->projectUserService->getAllProjectsWithUsers();

            return $this->successResponse('Projects retrieved successfully', $projects, 200);
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while retrieving the projects: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     * @param StorePivotRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePivotRequest $request)
    {
        $data = $request->only(['user_id', 'project_id', 'role', 'num_of_hours', 'last_activity']);
        $project = $this->projectUserService->add_to_pivot($data);
        return $this->successResponse('successefuly added ', $project, 201);
    }

    /**
     * Display the specified resource.
     * @param $user_id
     * @param $project_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($user_id, $project_id)
    {
        try {
            $project = $this->projectUserService->getUserProject($user_id, $project_id);

            return $this->successResponse('Project retrieved successfully', $project, 200);
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while retrieving the project: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param UpdatePivotRequest $request
     * @param $user_id 
     * @param $project_id
     * @return \Illuminate\Http\JsonResponse 
     */
    public function update(UpdatePivotRequest $request, $user_id, $project_id)
    {
        $validatedRequest = $request->validated();

        $record = $this->projectUserService->updatePivotData($user_id, $project_id, $validatedRequest);

        return $this->successResponse($record['data'], $record['message'], $record['status']);
    }


    /**
     * Remove the specified user from specified project
     * @param $user_id
     * @param $project_id
     * @return \Illuminate\Http\JsonResponse 
     */
    public function destroy($user_id, $project_id)
    {
        try {

            $this->projectUserService->removeUserFromProject($user_id, $project_id);

            return $this->successResponse('User removed from project successfully', null, 200);
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while removing the user from the project: ' . $e->getMessage(), 500);
        }
    }
}
