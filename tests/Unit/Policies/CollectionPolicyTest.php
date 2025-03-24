<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Collection;
use App\Models\User;
use App\Policies\CollectionPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionPolicyTest extends TestCase
{
    use RefreshDatabase;

    private CollectionPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new CollectionPolicy();
    }

    public function test_view_any_returns_true_for_authenticated_users(): void
    {
        $user = User::factory()->create();
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_view_returns_true_for_collection_owner(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($this->policy->view($user, $collection));
    }

    public function test_view_returns_false_for_non_owner(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this->assertFalse($this->policy->view($user, $collection));
    }

    public function test_create_returns_true_for_authenticated_users(): void
    {
        $user = User::factory()->create();
        $this->assertTrue($this->policy->create($user));
    }

    public function test_update_returns_true_for_collection_owner(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($this->policy->update($user, $collection));
    }

    public function test_update_returns_false_for_non_owner(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this->assertFalse($this->policy->update($user, $collection));
    }

    public function test_delete_returns_true_for_collection_owner(): void
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($this->policy->delete($user, $collection));
    }

    public function test_delete_returns_false_for_non_owner(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this->assertFalse($this->policy->delete($user, $collection));
    }
}
