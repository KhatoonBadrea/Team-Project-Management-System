<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\ApiResponseTrait;

class UserService
{
    use ApiResponseTrait;

    /**
     * get all user
     * @return User $user
     */

    public function getAllUser()
    {
        try {
            $user = User::all();
            if ($user->isNotEmpty()) {
                $user = UserResource::collection($user);
                return $user;
            } else
                return $this->notFound('there are not any user here');
        } catch (\Exception $e) {
            Log::error('Error in UserController@index' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . $e->getMessage(), [], 500);
        }
    }

    //========================================create_user===================================

    /**
     * create new user
     * @param array $data
     * @return User $user
     */

    public function create_user(array $data)
    {
        try {
            //Create a new user using the provided data
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            // Check if the user was created successfully
            if (!$user) {
                throw new \Exception('Failed to create the user.');
            }

            return UserResource::make($user)->toArray(request());
        } catch (\Exception $e) {
            Log::error('Error in UserController@create_user: ' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . 'there is an error in the server', 500);
        }
    }


    //========================================getUserById===================================

    /**
     * show the user information
     * @param string $id
     * @return User user
     */


    public function getUserById(string $id)
    {
        try {
            $user = User::find($id);
            if ($user) {
                return UserResource::make($user);
            } else {
                return $this->notFound('User not found.');
            }
        } catch (\Exception $e) {
            Log::error('Error in UserService@getUserById: ' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . $e->getMessage(), [], 500);
        }
    }

    //========================================updateUser===================================


    /**
     * update the user information
     * @param User $user
     * @param array $data
     * @return User $user
     */


    public function updateUser(User $user, array $data)
    {
        try {
            //check if the user is exists
            if (!$user->exists) {
                return $this->notFound('User not found.');
            }



            $user->update([
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
                'password' => isset($data['password']) ? Hash::make($data['password']) : $user->password,
            ]);



            // Return the created user as a resource
            return UserResource::make($user)->toArray(request());
        } catch (\Exception $e) {
            Log::error('Error in UserController@update: ' . $e->getMessage());
            return $this->errorResponse('An error occurred: ' . $e->getMessage(), 500);
        }
    }


    //========================================deleteUser===================================

    /**
     * delete the user
     * @param $id
     * 
     */

    public function deleteUser($id)
    {
        try {
            //check if the user is exists
            $user = User::find($id);

            if (!$user->exists) {

                return $this->notFound('user not found');
            }


            // delete the user
            $user->delete();

            return true;
        } catch (\Exception $e) {
            Log::error('Error in UserService@deleteUser: ' . 'there is an error in the server');
            return false;
        }
    }
}
