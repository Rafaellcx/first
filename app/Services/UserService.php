<?php

namespace App\Services;

use App\Http\Helpers\JsonFormat;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\UserRepositoryContract;
use App\Services\Contracts\UserServiceContract;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;

class UserService implements UserServiceContract
{
    protected UserRepositoryContract $userRepository;

    /**
     * @param UserRepositoryContract $userRepository
     */
    public function __construct(UserRepositoryContract $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(array $fields=[]): AnonymousResourceCollection
    {
        return UserResource::collection($this->userRepository->index($fields));
    }

    public function find(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return JsonFormat::error('User not found.');
        }

        return response()->json(new UserResource($user));
    }

    public function save(array $fields): JsonResponse
    {
        try {
            $this->userRepository->save($fields);
            return JsonFormat::success('User was saved successfully.',[],201);
        } catch (Exception) {

            return JsonFormat::error('Ops, User not saved.');
        }
    }

    public function changePassword(array $fields): JsonResponse
    {
        $user = $this->userRepository->find($fields['id']);

        if (!Hash::check($fields['password'], $user->password)) {
            return JsonFormat::error( 'The current password is incorrect.');
        }

        try {
            $user->password = Hash::make($fields['new_password']);
            $this->userRepository->changePassword($user);

            return JsonFormat::success('Password User was changed successfully.',[],201);
        } catch (Exception) {
            return JsonFormat::error('Ops, User not changed.');
        }
    }

    public function update(int $id, array $fields): JsonResponse
    {
        try {
            $this->userRepository->update($id, $fields);
        } catch (Exception) {

            return JsonFormat::error('Error when trying to update the data.');
        }
        return JsonFormat::success('User has been updated successfully.');
    }

    public function delete(int $id): JsonResponse
    {
        try {
            $this->userRepository->delete($id);
        } catch (Exception) {
            return JsonFormat::error('Ops, User was not deleted.');
        }

        return JsonFormat::success('',[],202);
    }
}
