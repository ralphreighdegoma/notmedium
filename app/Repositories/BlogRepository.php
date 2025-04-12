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

    public function forYou(): Array
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

        $query->where('status', 'published');

        return $query->with('user')->orderBy($sortBy, $descending ? 'desc' : 'asc')
            ->paginate($perPage, ['*'], 'page', $page)->toArray();
    }

    public function search(): Array
    {
        $page = request()->input('page', 1);
        $perPage = request()->input('perPage', 10);
        $sortBy = request()->input('sortBy', 'created_at');
        $descending = request()->input('descending', true);
        $search = request()->input('search', '');

        //current user id
        $currentUserId = Auth::id();
        $query = $this->model->where('created_by', $currentUserId);

        if ($search) {
            $query->where('title', 'like', '%' . $search . '%')
                ->orWhere('content', 'like', '%' . $search . '%');
        }

        return $query->with('user')->orderBy($sortBy, $descending ? 'desc' : 'asc')
            ->paginate($perPage, ['*'], 'page', $page)->toArray();
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
        return $this->model->with('user')->where('slug', $slug)->first();
    }

    public function slugGenerator(string $title)
    {
        //remove all special characters
        $slug = preg_replace('/[^a-zA-Z0-9\s]/', '', $title);
        //replace spaces with hyphens
        $slug = str_replace(' ', '-', $slug);
        //convert to lowercase
        $slug = strtolower($slug);
        //remove trailing hyphens
        $slug = rtrim($slug, '-');
        return $slug;
    }

    public function create(array $data)
    {
        //title must be unique 
        if ($this->model->where('title', $data['title'])->exists()) {
            throw new \Exception('Title must be unique');
        }
        

        if (isset($data['image']) && $data['image']) {
            $data['image'] = $data['image']->store('blogs', 'public');
        }

        $data['slug'] = $this->slugGenerator($data['title']);

        if ($this->model->where('slug', $data['slug'])->exists()) {
            $data['slug'] = $data['slug'] . '-' . uniqid();
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
        $data['slug'] = $this->slugGenerator($data['title']);

        if (isset($data['image']) && $data['image'] != null) {
            $data['image'] = $data['image']->store('blogs', 'public');
        }

        if ($this->model->where('slug', $data['slug'])->exists() && $this->model->where('id', '!=', $id)->first()) {
            $data['slug'] = $data['slug'] . '-' . uniqid();
        }

        $blog->update($data);
        return $blog;
    }

    public function delete(int $id): bool
    {
        return $this->model->destroy($id);
    }
}