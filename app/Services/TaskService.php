<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Project;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\TaskResource;
use App\Http\Traits\ApiResponseTrait;
use App\Events\TaskStatusUpdatedEvent;

class TaskService
{

    use ApiResponseTrait;

    //===================================================getAllTask==========================

    /**
     * fetch the all task from DB and fillter it
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllTask()
    {
        // try {
        // //   $tasks=Task::

        //     return TaskResource::collection($tasks);
        // } catch (\Exception $e) {
        //     Log::error('Error in TaskService@getAllTask: ' . $e->getMessage());
        //     return $this->errorResponse('An error occurred: there is an error in the server', 500);
        // }
    }




    //===================================================create_task==========================

    /**
     * create new task
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function create_task(array $data)
    {
        $user = JWTAuth::parseToken()->authenticate();

        try {
            $project = Project::find($data['project_id']);
            if (!$project) {
                return $this->errorResponse('Project not found', 404);
            }


            $task = Task::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'priority' => $data['priority'],
                'due_date' => null,
                'status' => 'pending',
                'project_id' => $data['project_id'],
                'assigned_to' => $data['assigned_to'],
                'owner_id' => $user->id,
                'deadline' => $data['deadline'],
                'note' => null,
            ]);
            return TaskResource::make($task)->toArray(request());
        } catch (\Exception $e) {
            Log::error('Error in TaskService@create_task: ' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . 'there is an error in the server', 500);
        }
    }


    //===================================================update_task==========================

    /**
     * update the task
     * @param Task $task
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_task(Task $task, array $data)
    {
        try {
            // dd($task);

            if (!$task->exists) {
                return $this->notFound('Task not found.');
            }
            //   Update only the fields that are provided in the data array
            $task->update(array_filter([
                'title' => $data['title'] ?? $task->title,
                'description' => $data['description'] ?? $task->description,
                'priority' => $data['priority'] ?? $task->priority,
                'due_date' => $data['due_date'] ?? $task->due_date,
                'status' => $data['status'] ?? $task->status,
                'project_id' => $data['project_id'] ?? $task->project_id,
                'assigned_to' => $data['assigned_to'] ?? $task->assigned_to,
                'deadline' => $data['deadline'] ?? $task->deadline,
                'note' => $data['note'] ?? $task->note,

            ]));

            // Return the updated task as a resource
            return TaskResource::make($task)->toArray(request());
            // return $task;
        } catch (\Exception $e) {
            Log::error('Error in TaskService@update_Task' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . 'there is an error in the server', [], 500);
        }
    }


    //===================================================delete_task==========================

    /**
     * Delete the task
     * @param Task $task
     */
    public function delete_task(Task $task)
    {
        try {
            if (!$task->exists) {
                return $this->notFound('Task not found.');
            }

            $task->delete();
        } catch (\Exception $e) {
            Log::error('Error in TaskService@delete_task: ' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . 'there is an error in the server', 500);
        }
    }


    //===================================================update_assigned_to==========================

    /**
     * update assigned to in the task
     * @param Task $task
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */

    public function update_assigned_to(Task $task, array $data)
    {
        try {

            if (!$task->exists) {
                return $this->notFound('Task not found.');
            }
            //   Update only the fields that are provided in the data array
            $task->update(array_filter([
                'due_date' => null,
                'status' => 'in-progress',
                'assigned_to' => $data['assigned_to'] ?? $task->assigned_to,
                'deadline' => $data['deadline'] ?? $task->deadline,
            ]));


            // Return the updated task as a resource
            return TaskResource::make($task)->toArray(request());
        } catch (\Exception $e) {
            Log::error('Error in TaskService@update_Task' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . 'there is an error in the server', [], 500);
        }
    }


    //===================================================get_assigned_task_for_user==========================

    /**
     * get all task that assigned to the one user
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_assigned_task_for_user()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $task = Task::where('assigned_to', $user->id)->get();

            return TaskResource::collection($task);
        } catch (\Exception $e) {
            Log::error('Error in TaskService@get_assigned_task_for_user: ' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . 'there is an error in the server', 500);
        }
    }

    //===================================================show_task ==========================

    /**
     * show the specific task
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */

    public function show_task(Task $task)
    {
        try {

            if (!$task->exists) {
                return $this->notFound('Task not found.');
            }

            return TaskResource::make($task)->toArray(request());
        } catch (\Exception $e) {
            Log::error('Error in TaskService@show_task: ' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . 'there is an error in the server', 500);
        }
    }


    public function update_status(Task $task, array $data)
    {
        try {

            $task->status = $data['status'] ?? $task->status;
            $task->save();
            event(new TaskStatusUpdatedEvent($task));
            return $task;
        } catch (\Exception $e) {
            Log::error('Error in TaskService@update_status: ' . $e->getMessage());
            return $this->errorResponse('An error occurred: there is an error in the server', 500);
        }
    }
}
