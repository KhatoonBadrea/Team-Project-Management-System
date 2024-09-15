<?php

namespace App\Http\Controllers\Api\project;

use App\Services\ProjectService;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{

    use ApiResponseTrait;


    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $last_task = $request->input('last_task');
        $old_task = $request->input('old_task');
        $height_priority = $request->input('height_priority');


        $project = $this->projectService->get_all_project($last_task, $old_task, $height_priority);

        return $this->successResponse('this is all projects', $project, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        // dd($request);
        $validationdata = $request->validated();
        $project = $this->projectService->create_project($validationdata);
        return $this->successResponse('successefuly added the project', $project, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $validatedRequest = $request->validated();
        $project = $this->projectService->update_project($project, $validatedRequest);
        return $this->successResponse($project, 'project updated successfully.', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
