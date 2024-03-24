<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface UserRepositoryContract
{
    public function index(array $fields);

    public function find(int $id);

    public function save(array $fields);

    public function changePassword(Model $model);

    public function update(int $id, array $fields);

    public function delete(int $id);
}
