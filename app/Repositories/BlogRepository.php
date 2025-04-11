<?php

namespace App\Repositories;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class BlogRepository implements BlogRepositoryInterface
{
    protected $model;

    public function __construct(Blog $blog)
    {
        $this->model = $blog;
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function search(): Array
    {
        $page = request()->input('page', 1);
        $perPage = request()->input('perPage', 10);
        $sortBy = request()->input('sortBy', 'created_at');
        $descending = request()->input('descending', true);
        $search = request()->input('search', '');

        $query = $this->model->query();
        if ($search) {
            $query->where('title', 'like', '%' . $search . '%')
                ->orWhere('content', 'like', '%' . $search . '%');
        }

        return $query->orderBy($sortBy, $descending ? 'desc' : 'asc')
            ->paginate($perPage, ['*'], 'page', $page); 
    }

    public function getById(int $id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Get a blog by its slug
     *
     * @param string $slug
     * @return \App\Models\Blog|null
     */
    public function getBySlug(string $slug)
    {
        $slug = str_replace('-', ' ', $slug);
        return $this->model->where('title', $slug)->first();
    }

    public function create(array $data)
    {
        //title must be unique 
        if ($this->model->where('title', $data['title'])->exists()) {
            throw new \Exception('Title must be unique');
        }
        $data['created_by'] = Auth::id();
        return $this->model->create($data);
    }

    public function updateStatus(int $id, string $status)
    {
        $blog = $this->getById($id);
        $blog->status = $status;
        $blog->save();
        return $blog;
    }

    public function update(int $id, array $data)
    {
        $blog = $this->getById($id);
        $blog->update($data);
        return $blog;
    }

    public function delete(int $id): bool
    {
        return $this->model->destroy($id);
    }
}