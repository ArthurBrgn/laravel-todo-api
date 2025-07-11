<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SearchTagRequest;
use App\Http\Requests\StoreTagRequest;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

final class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project): ResourceCollection
    {
        return $project->tags()
            ->simplePaginate()
            ->toResourceCollection();
    }

    /**
     * Search for tags by project
     */
    public function search(Project $project, SearchTagRequest $request): ResourceCollection
    {
        $search = $request->validated('search');

        return $project->tags()
            ->where('name', 'like', "%{$search}%")
            ->simplePaginate()
            ->toResourceCollection();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Project $project, StoreTagRequest $request): JsonResponse
    {
        $tag = $project->tags()->create([
            'name' => $request->validated('name'),
        ]);

        return response()->json(
            $tag->toResource(),
            Response::HTTP_CREATED
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTagRequest $request, Tag $tag): JsonResponse
    {
        $tag->update($request->validated());

        return response()->json($tag->toResource());
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(Tag $tag): JsonResponse
    {
        $tag->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
