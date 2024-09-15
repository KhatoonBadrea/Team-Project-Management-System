<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePivotRequest extends FormRequest
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
        $last_activity = $this->input('last_activity');

        if ($last_activity) {
            try {
                // Attempt to parse with seconds
                $last_activity = Carbon::createFromFormat('d-m-Y H:i:s', $last_activity);
            } catch (\Exception $e) {
                try {
                    // Attempt to parse without seconds
                    $last_activity = Carbon::createFromFormat('d-m-Y H:i', $last_activity);
                } catch (\Exception $e) {
                    throw new HttpResponseException(response()->json([
                        'status' => 'error',
                        'message' => 'Invalid last_activity format.',
                        'errors' => ['last_activity' => 'The last_activity must match the format d-m-Y H:i or d-m-Y H:i:s.']
                    ]));
                }
            }

            // Merge the correctly formatted date back into the request
            $this->merge([
                'last_activity' => $last_activity->format('Y-m-d H:i:s'),
            ]);
        }
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'project_id' => 'required|integer|exists:projects,id',
            'role' => 'required|string|in:tester,manager,developer',
            'num_of_hours' => 'nullable|integer|min:0',
            'last_activity' => 'nullable|date'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Please make sure the inputs are correct.',
            'errors' => $validator->errors(),
        ]));
    }

    public function attributes()
    {
        return [
            'user_id' => 'User ID',
            'project_id' => 'Project ID',
            'role' => 'User Role in Project',
            'num_of_hours' => 'Number of hours',
            'last_activity' => 'Last activity date and time',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'User ID is required.',
            'project_id.required' => 'Project ID is required.',
            'role.required' => 'Role is required ',
            'role.in' => 'Role  must be either tester, manager, or developer.',
            'num_of_hours.required' => 'Number of hours is required and must be a positive integer.',
            // 'last_activity.before' => 'Last activity must be a date before now.',
        ];
    }
}
