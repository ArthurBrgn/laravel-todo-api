<?php

declare(strict_types=1);

// tests/Traits/WithSanctumUser.php

namespace Tests\Traits;

use App\Models\User;
use Laravel\Sanctum\Sanctum;

trait WithSanctumUser
{
    public function authenticateUser()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        return $user;
    }
}
