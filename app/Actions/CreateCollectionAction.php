<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Collection;
use App\Models\User;

class CreateCollectionAction
{
    public function handle(User $user, array $data): Collection
    {
        $collection = new Collection([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
        $collection->user()->associate($user);
        $collection->save();

        return $collection;
    }
}
