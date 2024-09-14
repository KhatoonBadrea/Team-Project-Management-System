<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Http\Traits\ApiResponseTrait;


class ProjectUserService
{
    use ApiResponseTrait;


    public function add_to_pivot(array $data)
    {

        $user = User::findOrFail($data['user_id']);
 

        $user->projects()->attach($data['project_id'], [
            'role' => $data['role'],
            'num_of_hours' => $data['num_of_hours'],
            'last_activity' => $data['last_activity']
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
}
