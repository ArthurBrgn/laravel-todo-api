<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Http\Response;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        return $project->tags()->simplePaginate()
            ->toResourceCollection();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Project $project, StoreTagRequest $request)
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
    public function update(StoreTagRequest $request, Tag $tag)
    {
        $tag->update($request->validated());

        return response()->json($tag->toResource());
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->noContent();
    }
}
