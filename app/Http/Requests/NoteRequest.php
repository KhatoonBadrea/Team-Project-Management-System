<?php

namespace App\Http\Requests;

use App\Models\Task;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Foundation\Http\FormRequest;

class NoteRequest extends FormRequest
{
    use ApiResponseTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = JWTAuth::parseToken()->authenticate();

        // Get the task from the route
        $task = $this->route('task');

        if (!$task->exists) {
            return $this->notFound('Task not found.');
        }
        // Get the project related to the task
        $project = $task->project;

        // Check if the user is part of the project and has the 'tester' role
        $userRoleInProject = $project->users()
            ->where('user_id', $user->id)
            ->wherePivotIn('role', ['tester'])
            ->exists();

        // Only allow if the user has the 'tester' role in the project
        return $userRoleInProject;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'note' => 'nullable|max:500|string'
        ];
    }
}
