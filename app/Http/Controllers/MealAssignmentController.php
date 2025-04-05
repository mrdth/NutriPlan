<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\MealAssignment;
use App\Models\MealPlanDay;
use App\Models\MealPlanRecipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MealAssignmentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'meal_plan_day_id' => 'required|exists:meal_plan_days,id',
            'meal_plan_recipe_id' => 'required|exists:meal_plan_recipe,id',
            'servings' => 'required|numeric|min:0.1|max:20',
        ]);

        try {
            DB::beginTransaction();

            $mealPlanDay = MealPlanDay::findOrFail($validated['meal_plan_day_id']);
            $mealPlanRecipe = MealPlanRecipe::findOrFail($validated['meal_plan_recipe_id']);

            // Check if this meal plan recipe is already assigned to this day
            if ($mealPlanDay->mealAssignments->contains('meal_plan_recipe_id', $mealPlanRecipe->id)) {
                throw ValidationException::withMessages([
                    'meal_plan_recipe_id' => ['This recipe is already assigned to this day.'],
                ]);
            }

            // Check if there are enough servings available
            if ($mealPlanRecipe->servings_available < $validated['servings']) {
                throw ValidationException::withMessages([
                    'servings' => ['Not enough servings available.'],
                ]);
            }

            // Create the assignment
            $assignment = new MealAssignment($validated);
            $assignment->save();

            // Update available servings
            $mealPlanRecipe->servings_available -= $validated['servings'];
            $mealPlanRecipe->save();

            DB::commit();

            return redirect()->back()->with('success', 'Meal assigned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof ValidationException) {
                throw $e;
            }
            report($e);
            return redirect()->back()->withErrors(['error' => 'Failed to assign meal. Please try again.']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @throws ValidationException
     */
    public function update(Request $request, MealAssignment $mealAssignment): RedirectResponse
    {
        $validated = $request->validate([
            'servings' => 'required|numeric|min:0.1|max:20',
        ]);

        try {
            DB::beginTransaction();

            $mealPlanRecipe = $mealAssignment->mealPlanRecipe;
            $servingsDiff = $validated['servings'] - $mealAssignment->servings;

            // Check if there are enough servings available for the increase
            if ($servingsDiff > 0 && $mealPlanRecipe->servings_available < $servingsDiff) {
                throw ValidationException::withMessages([
                    'servings' => ['Not enough servings available for the increase.'],
                ]);
            }

            // Update available servings
            $mealPlanRecipe->servings_available -= $servingsDiff;
            $mealPlanRecipe->save();

            // Update the assignment
            $mealAssignment->servings = $validated['servings'];
            $mealAssignment->save();

            DB::commit();

            return redirect()->back()->with('success', 'Meal assignment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof ValidationException) {
                throw $e;
            }
            report($e);
            return redirect()->back()->withErrors(['error' => 'Failed to update meal assignment. Please try again.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MealAssignment $mealAssignment): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Return servings to available pool
            $mealPlanRecipe = $mealAssignment->mealPlanRecipe;
            $mealPlanRecipe->servings_available += $mealAssignment->servings;
            $mealPlanRecipe->save();

            // Delete the assignment
            $mealAssignment->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Meal assignment removed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->back()->withErrors(['error' => 'Failed to remove meal assignment. Please try again.']);
        }
    }
}
