<?php

declare(strict_types=1);

test('logout successfully', function () {
    $user = $this->authenticateUser();

    $response = $this->postJson(route('auth.logout'));

    $response->assertNoContent();

    $this->assertDatabaseMissing('personal_access_tokens', [
        'tokenable_id' => $user->id,
    ]);
});
