<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordUserRequest;
use App\Http\Requests\PerPageRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\Contracts\UserServiceContract;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private UserServiceContract $userService;

    /**
     * @param UserServiceContract $userService
     */
    public function __construct(UserServiceContract $userService)
    {
        $this->userService = $userService;
    }

    public function index(PerPageRequest $request)
    {
        return $this->userService->index($request->validated());
    }

    public function show($id)
    {
        return $this->userService->find($id);
    }

    public function store(StoreUserRequest $request)
    {
        return $this->userService->save($request->validated());
    }

    public function changePassword(ChangePasswordUserRequest $request)
    {
        return $this->userService->changePassword($request->validated());
    }

    public function update(int $id, UpdateUserRequest $request)
    {
        return $this->userService->update($id, $request->validated());
    }

    public function destroy(int $id)
    {
        return $this->userService->delete($id);
    }
}
