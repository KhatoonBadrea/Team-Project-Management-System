<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Contracts\Validation\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $task = $this->route('task');

        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return false;
        }


        if ($task->owner_id === $user->id) {
            return true;
        }

        return false;
    }




    public function prepareForValidation()
    {
        $dueDate = $this->input('due_date');
        $deadline = $this->input('deadline');

        $this->merge([
            'due_date' => $dueDate ? Carbon::parse($dueDate)->format('Y-m-d') : null,
            'deadline' => $deadline ? Carbon::parse($deadline)->format('Y-m-d') : null,
        ]);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'priority' => 'nullable|string|in:height,low,medium',
            'due_date' => 'nullable|date|after:now',
            'status' => 'nullable|string|in:pending,done,in-progress',
            'assigned_to' => 'nullable|integer|exists:users,id',
            'deadline' => 'nullable|date|after:now',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'please make sure for the inputs  ',
            'errors' => $validator->errors(),

        ]));
    }

    public function attributes()
    {
        return [
            'title' => 'title',
            'description' => 'description',
            'priority' => 'priority',
            'due_date' => 'due_date',
            'status' => 'status',
            'assigned_to' => 'assigned_to',
            'deadline' => 'deadline',
        ];
    }

    public function messages()
    {
        return [
            'date' => 'The :attribute must be a valid date',
            'due_date.after' => 'The due date must be a date after now',
            'deadline.after' => 'The deadline must be a date after now',
            'assigned_to.exists' => 'The selected user does not exist.',
            'priority.in' => 'The priority must be one of the following values:low,medium,height',
            'status.in' => 'The status must be one of the following values: pending, in-progress, done.',
        ];
    }
}
