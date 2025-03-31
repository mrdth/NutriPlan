<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\MealPlan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class MealPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $mealPlans = MealPlan::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return Inertia::render('MealPlans/Index', [
            'mealPlans' => $mealPlans,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('MealPlans/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'duration' => 'required|integer|in:7,14',
            'people_count' => 'required|integer|min:1|max:20',
        ]);

        /** @var User|null $user */
        $user = $request->user();

        if ($user !== null) {
            $user->mealPlans()->create($validated);
        }

        return redirect()->route('meal-plans.index')
            ->with('success', 'Meal plan created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MealPlan $mealPlan): Response
    {
        Gate::authorize('view', $mealPlan);

        // Eager load recipes with their pivot data
        $mealPlan->load(['recipes' => function (\Illuminate\Database\Eloquent\Relations\BelongsToMany $query): void {
            $query->with('user:id,name,slug');
        }]);

        return Inertia::render('MealPlans/Show', [
            'mealPlan' => $mealPlan,
            'availableMealPlans' => Auth::user()->mealPlans()
                ->whereNot('id', $mealPlan->id)
                ->select('id', 'name', 'start_date')
                ->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): null
    {
        return null;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): null
    {
        return null;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MealPlan $mealPlan): RedirectResponse
    {
        Gate::authorize('delete', $mealPlan);

        $mealPlan->delete();

        return redirect()->route('meal-plans.index')
            ->with('success', 'Meal plan deleted successfully.');
    }
}
