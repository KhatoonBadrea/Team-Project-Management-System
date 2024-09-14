<?php

namespace App\Http\Controllers\Api\project;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ProjectUserService;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\StorePivotRequest;

class ProjectUserController extends Controller
{

    use ApiResponseTrait;

    protected $projectUserService;

    public function __construct(ProjectUserService $projectUserService)
    {
        $this->projectUserService = $projectUserService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePivotRequest $request)
    {
        $data = $request->only(['user_id', 'project_id', 'role', 'num_of_hours', 'last_activity']);
        $project = $this->projectUserService->add_to_pivot($data);
        // dd($project);
        return $this->successResponse('successefuly added ', $project, 201);
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
