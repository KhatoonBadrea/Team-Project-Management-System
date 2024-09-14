<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use App\Rules\AssignRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $dueDate = $this->input('due_date');
        $deadline = $this->input('deadline');
    
        if ($dueDate) {
            try {
                $dueDate = Carbon::createFromFormat('d-m-Y H:i', $dueDate);
            } catch (\Exception $e) {
                throw new HttpResponseException(response()->json([
                    'status' => 'error',
                    'message' => 'Invalid due_date format.',
                    'errors' => ['due_date' => 'The due_date must match the format d-m-Y H:i.']
                ]));
            }
        }
    
        if ($deadline) {
            try {
                $deadline = Carbon::createFromFormat('d-m-Y H:i', $deadline);
            } catch (\Exception $e) {
                throw new HttpResponseException(response()->json([
                    'status' => 'error',
                    'message' => 'Invalid deadline format.',
                    'errors' => ['deadline' => 'The deadline must match the format d-m-Y H:i.']
                ]));
            }
        }
    
        // تأكد من استخدام التنسيق الصحيح الذي تدعمه قاعدة البيانات
        $this->merge([
            'due_date' => $dueDate ? $dueDate->format('Y-m-d H:i:s') : null,
            'deadline' => $deadline ? $deadline->format('Y-m-d H:i:s') : null,
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'priority' => 'required|string|in:height,low,medium',
            'due_date' => 'nullable|date|after:now',
            'assigned_to' => ['required', 'integer', 'exists:users,id', new AssignRule($this->input('project_id'))],
            'deadline' => 'required|date|after:now',
            'project_id' => 'required|integer|exists:projects,id',
            'note'=>'nullable|string|max:1000',
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
            'assigned_to' => 'assigned_to',
            'deadline' => 'deadline',
            'project_id' => 'project_id'
        ];
    }
    public function messages()
    {
        return [
            'required' => ':attribute is required',
            'date' => 'The :attribute must be a valid date',
            'due_date.after' => 'The due date must be a date after today.',
            'deadline.after' => 'The deadline must be a date after now.',
            'assigned_to.exists' => 'The selected user does not exist.',
            'priority.in' => 'The priority must be one of the following values: low,medium, height',
            'status.in' => 'The status must be one of the following values: pending, in-progress, done.',
        ];
    }
}
