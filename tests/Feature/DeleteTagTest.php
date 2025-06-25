<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\Tag;

test('delete tag successfully', function () {
    $tag = Tag::factory()
        ->for(Project::factory())
        ->create();

    $response = $this->deleteJson("/api/tags/{$tag->id}");

    $response->assertNoContent();

    $this->assertSoftDeleted($tag);
});
