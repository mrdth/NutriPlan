<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Recipe;
use App\Models\User;

class RecipePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Recipe $recipe): bool
    {
        // Recipe owner can always view their own recipes
        if ($user instanceof \App\Models\User && $user->id === $recipe->user_id) {
            return true;
        }

        // Public recipes can be viewed by any user
        if ($recipe->is_public) {
            return true;
        }

        // Private recipes can only be viewed by their owner
        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Recipe $recipe): bool
    {
        return $user->id === $recipe->user_id;
    }

    public function delete(User $user, Recipe $recipe): bool
    {
        return $user->id === $recipe->user_id;
    }
}
