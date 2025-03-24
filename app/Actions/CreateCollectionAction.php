<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Collection;
use App\Models\User;

class CreateCollectionAction
{
    public function handle(User $user, array $data): Collection
    {
        $collection = new Collection();
        $collection->user()->associate($user);
        $collection->name = $data['name'];
        $collection->description = $data['description'] ?? null;
        $collection->save();

        return $collection;
    }
}
