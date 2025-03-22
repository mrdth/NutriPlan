<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Recipe;
use App\Models\User;
use App\Policies\RecipePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RecipePolicyTest extends TestCase
{
    use RefreshDatabase;

    private RecipePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new RecipePolicy();
    }

    #[Test]
    public function user_can_view_any_recipes(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($this->policy->viewAny($user));
    }

    #[Test]
    public function user_can_view_their_own_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user)->create();

        $this->assertTrue($this->policy->view($user, $recipe));
    }

    #[Test]
    public function user_can_view_other_users_published_recipes(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $recipe = Recipe::factory()
            ->for($otherUser)
            ->published()
            ->create();

        $this->assertTrue($this->policy->view($user, $recipe));
    }

    #[Test]
    public function user_cannot_view_other_users_draft_recipes(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $recipe = Recipe::factory()
            ->for($otherUser)
            ->draft()
            ->create();

        $this->assertFalse($this->policy->view($user, $recipe));
    }

    #[Test]
    public function user_can_create_recipe(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($this->policy->create($user));
    }

    #[Test]
    public function user_can_update_their_own_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user)->create();

        $this->assertTrue($this->policy->update($user, $recipe));
    }

    #[Test]
    public function user_cannot_update_other_users_recipe(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $recipe = Recipe::factory()->for($otherUser)->create();

        $this->assertFalse($this->policy->update($user, $recipe));
    }

    #[Test]
    public function user_can_delete_their_own_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user)->create();

        $this->assertTrue($this->policy->delete($user, $recipe));
    }

    #[Test]
    public function user_cannot_delete_other_users_recipe(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $recipe = Recipe::factory()->for($otherUser)->create();

        $this->assertFalse($this->policy->delete($user, $recipe));
    }
}
