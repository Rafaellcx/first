<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class UserRepository implements UserRepositoryContract
{
    private User $model;
    /**
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function index(array $fields): Collection|LengthAwarePaginator|array
    {
        $users = $this->model->query();

        if (isset($fields['paginate'])) return $users->paginate($fields['paginate']);

        return $users->get();
    }

    public function find(int $id): Model|Collection|Builder|array|null
    {
        return $this->model->query()->find($id);
    }

    public function save(array $fields): Model|Builder
    {
        return $this->model->query()->create($fields);
    }

    public function changePassword(Model $model): bool
    {
        return $model->save();
    }

    public function update(int $id, array $fields): bool|int
    {
        return $this->model->query()->find($id)->update($fields);
    }

    public function delete(int $id)
    {
        return $this->model->query()->where('id', $id)->delete();
    }
}
