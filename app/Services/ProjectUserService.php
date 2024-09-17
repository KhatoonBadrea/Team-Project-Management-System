<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\ApiResponseTrait;


class ProjectUserService
{
    use ApiResponseTrait;


    /**
     * add data to pivote table
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */

    public function add_to_pivot(array $data)
    {

        $user = User::findOrFail($data['user_id']);


        $user->projects()->attach($data['project_id'], [
            'role' => $data['role'],
            'num_of_hours' => 0,
            'last_activity' => now(),
        ]);

        $pivotData = $user->projects()->where('project_id', $data['project_id'])->first()->pivot;

        $formattedDate = Carbon::parse($pivotData->last_activity)->format('d-m-Y H:i:s');


        return $this->successResponse([
            'user_id' => $data['user_id'],
            'project_id' => $data['project_id'],
            'role' => $pivotData->role,
            'num_of_hours' => $pivotData->num_of_hours,
            'last_activity' => $formattedDate,
        ], 'Data added successfully');
    }

    /**
     * get user & the project that work on it with a details from pivot
     */
    public function getAllProjectsWithUsers()
    {
        return User::with(['projects' => function ($query) {
            $query->select('name', 'description');
        }])
            ->get();
    }



    /**
     * get the specified project of the  specified user
     * @param $user_id
     * @param $project
     */
    public function getUserProject($user_id, $project_id)
    {
        $user = User::findOrFail($user_id);
        return $user->projects()->where('project_id', $project_id)->firstOrFail();
    }


    /**
     * update record in pivot table
     * @param $user_id
     * @param $project_id
     * @param $data
     * @return \Illuminate\Http\JsonResponse

     */
    public function updatePivotData($user_id, $project_id, $data)
    {
        try {
            $user = User::findOrFail($user_id);

            $user->projects()->updateExistingPivot($project_id, [
                'role' => $data['role'],
                'num_of_hours' => $data['num_of_hours'] ?? 0,
                'last_activity' => $data['last_activity'] ?? now(),
            ]);

            $pivotData = $user->projects()->where('project_id', $project_id)->first()->pivot;

            return [
                'message' => 'Pivot data updated successfully',
                'data' => [
                    'user_id' => $user_id,
                    'project_id' => $project_id,
                    'role' => $pivotData->role,
                    'num_of_hours' => $pivotData->num_of_hours,
                    'last_activity' => $pivotData->last_activity,
                ],
                'status' => 200,
            ];
        } catch (\Exception $e) {
            return [
                'message' => 'An error occurred while updating the pivot data: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    /**
     * remove the specified user from the  specified project
     * @param $user_id
     * @param $project
     */
    public function removeUserFromProject($user_id, $project_id)
    {
        $user = User::findOrFail($user_id);
        $user->projects()->detach($project_id);
    }
}
