<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\BlogRepositoryInterface;
use App\Http\Requests\CreateBlogRequest;
use App\Http\Requests\UpdateBlogRequest;

class BlogController extends Controller
{
    protected $blogRepository;

    public function __construct(BlogRepositoryInterface $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }

    public function index()
    {
        $blogs = $this->blogRepository->search();

        return response()->json([
            'status' => 'success',
            'data' => $blogs
        ]);
    }

    public function forYou()
    {
        $blogs = $this->blogRepository->forYou();

        return response()->json([
            'status' => 'success',
            'data' => $blogs
        ]);
    }

    public function show($id)
    {
        $blog = $this->blogRepository->getById($id);

        return response()->json([
            'status' => 'success',
            'data' => $blog
        ]);
    }

    public function store(CreateBlogRequest $request)
    {
        $blog = $this->blogRepository->create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Blog created successfully',
            'data' => $blog
        ], 201);
    }

    public function update(UpdateBlogRequest $request, $id)
    {
        $blog = $this->blogRepository->update($id, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Blog updated successfully',
            'data' => $blog
        ]);
    }

    public function destroy($id)
    {
        $this->blogRepository->delete($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Blog deleted successfully'
        ]);
    }

    /**
     * Preview a blog by slug
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function preview($slug)
    {
        $blog = $this->blogRepository->getBySlug($slug);

        if (!$blog) {
            return response()->json([
                'status' => 'error',
                'message' => 'Blog not found'
            ], 404);
        }

        return response()->json($blog);
    }

    /**
     * Update the status of a blog
     *
     * @param string $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $blog = $this->blogRepository->updateStatus($id, $request->status);

        return response()->json($blog);
    }
}