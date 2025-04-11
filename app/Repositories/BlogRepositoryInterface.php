<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface BlogRepositoryInterface
{
    public function getAll(): Collection;

    public function search(): Array;

    public function getById(int $id);

    public function getBySlug(string $slug);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id): bool;

    public function updateStatus(int $id, string $status);
}