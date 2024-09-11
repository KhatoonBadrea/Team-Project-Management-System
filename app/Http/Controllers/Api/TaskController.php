<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Services\TaskService;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\updateStatusRequest;
use App\Http\Requests\updateAssignedRequest;

class TaskController extends Controller
{
    use ApiResponseTrait;


    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }


    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     */
    public function index(Request $request)
    {
        $priority = $request->input('priority');
        $status = $request->input('status');

        $tasks = $this->taskService->getAllTask($priority, $status);

        return $this->successResponse('this is all tasks', $tasks, 200);
    }


    /**
     * Store a newly created resource in storage.
     * @param StoreTaskRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTaskRequest $request)
    {
        $validationdata = $request->validated();
        $task = $this->taskService->create_task($validationdata);
        return $this->successResponse('successefuly added the task', $task, 201);
    }


    /**
     * Display the specified resource.
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Task $task)
    {
        $task = $this->taskService->show_task($task);
        return $this->successResponse('this is the task', $task, 200);
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateTaskRequest $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $validatedRequest = $request->validated();
        $newTask = $this->taskService->update_task($task, $validatedRequest);
        return $this->successResponse($newTask, 'Task updated successfully.', 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param Task $task
     */
    public function destroy(Task $task)
    {
        $this->taskService->delete_task($task);
        return $this->successResponse('Task deleted successfully.', [], 200);
    }


    /**
     * get the task that assigned for the specific user
     * @return \Illuminate\Http\JsonResponse
     */

    public function get_my_task()
    {
        $tasks = $this->taskService->get_assigned_task_for_user();
        return $this->successResponse('this is all tasks that assigned for you', $tasks, 200);
    }


    /**
     * update the assigned to for the specific task
     * @param updateAssignedRequest $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_assigned_to(updateAssignedRequest $request, Task $task)
    {
        $validatedRequest = $request->validated();
        $newAssigne = $this->taskService->update_assigned_to($task, $validatedRequest);
        return $this->successResponse($newAssigne, 'Assigned_to updated successfully.', 200);
    }



    /**
     * user can update the status for the task that assigned to him
     * @param updateStatusRequest $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(updateStatusRequest $request, Task $task)
    {
        $validatedRequest = $request->validated();

        $newTask = $this->taskService->update_status($task, $validatedRequest);

        return $this->successResponse($newTask, 'Task updated successfully.', 200);
    }
}
