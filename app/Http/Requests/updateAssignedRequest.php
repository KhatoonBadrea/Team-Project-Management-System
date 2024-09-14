<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class updateAssignedRequest extends FormRequest
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

        if ($user->state === 'admin') {
            return true;
        }
        // the manager can only update on the task who created it
        if ($user->state === 'manager' && $task->owner_id === $user->id) {
            return true;
        }

        return false;
    }


    public function prepareForValidation()
    {
        $deadline = $this->input('deadline');

        $this->merge([
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

            'assigned_to' => 'assigned_to',
            'deadline' => 'deadline',

        ];
    }

    public function messages()
    {
        return [

            'assigned_to.exists' => 'The selected user does not exist.',
            'deadline.after' => 'The deadline must be a date after now',


        ];
    }
}
