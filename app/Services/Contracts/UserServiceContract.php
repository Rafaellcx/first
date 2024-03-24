<?php

namespace App\Services\Contracts;

interface UserServiceContract
{
    public function index(array $fields);

    public function find(int $id);

    public function save(array $fields);

    public function changePassword(array $fields);

    public function update(int $id, array $fields);

    public function delete(int $id);
}
