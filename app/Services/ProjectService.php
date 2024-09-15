<?php

namespace App\Services;

use App\Models\Project;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\TaskResource;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Resources\ProjectResource;

class ProjectService
{
    use ApiResponseTrait;

    public function create_project(array $data)
    {
        $user = JWTAuth::parseToken()->authenticate();
        // dd($user->id);
        try {

            $project = Project::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'owner_id' => $user->id,
            ]);


            return ProjectResource::make($project)->toArray(request());
        } catch (\Exception $e) {
            Log::error('Error in ProjectService@create_project: ' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . 'there is an error in the server', 500);
        }
    }

    public function get_all_project($last_task = null, $old_task = null, $height_priority = null)
    {
        // try {

        if ($last_task) {
            $projects = Project::with('lastTask')->get();
            return $projects;
        }
        if ($old_task) {
            $projects = Project::with('oldTask')->get();
            return $projects;
        }
        if ($height_priority) {
            $project = Project::with('heightPriority')->get();
            return $project;
        }

        $projects = Project::all();
        return ProjectResource::collection($projects);
        // } catch (\Exception $e) {
        //     Log::error('Error in ProjectService@get_all_project: ' . $e->getMessage());
        //     return $this->errorResponse('An error occurred: ' . 'there is an error in the server', 500);
        // }
    }

    public function update_project(Project $project, array $data)
    { {
            try {
                // dd($project);

                if (!$project->exists) {
                    return $this->notFound('Project not found.');
                }
                //   Update only the fields that are provided in the data array
                $project->update(array_filter([
                    'name' => $data['name'] ?? $project->title,
                    'description' => $data['description'] ?? $project->description,
                ]));

                // Return the updated project as a resource
                return ProjectResource::make($project)->toArray(request());
                // return $project;
            } catch (\Exception $e) {
                Log::error('Error in ProjectService@update_Project' . $e->getMessage());
                return $this->errorResponse('An error occurred: ' . 'there is an error in the server', [], 500);
            }
        }
    }
}
