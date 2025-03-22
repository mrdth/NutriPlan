<?php

declare(strict_types=1);

use App\Models\User;

test('to array only includes visible attributes', function () {
    $user = User::factory()->create()->refresh();

    expect(array_keys($user->toArray()))
        ->toBe([
            'id',
            'name',
        ]);
});
