# Meal Planning - Phase 7b: Automatic Meal Plan Synchronization

## Overview
This phase focuses on making generated shopping lists dynamic. They will automatically update when relevant changes occur in the associated Meal Plan assignments, ensuring the shopping list reflects the current state of planned meals marked "to cook".

## Depends On
- Phase 7a: Shopping List Generation
- Laravel Event System

## Leads To
- Phase 7c: Enhancements (Unit Conversion, Categorization)

## Core Functionality

### Automatic Updates
- Shopping lists generated from a meal plan (Phase 7a) will automatically update in response to specific changes in `MealAssignment` records within the list's `start_date` and `end_date` range.
- Updates trigger when:
    - A new meal is assigned to a day within the list's date range AND marked as "to cook".
    - An existing assigned meal's "to cook" status changes (within the date range).
    - An assigned meal is removed from a day (within the date range).
    - An assigned meal's serving size is adjusted (within the date range).

### Update Logic
- When an event occurs, the system identifies any potentially affected `ShoppingList` (based on `start_date`, `end_date`, and potentially a link back to the MealPlan if we reconsider that, but date range is primary).
- The relevant `ShoppingListItem` quantities are recalculated based *only* on the change (additive or subtractive based on the event).
- If removing a meal causes an ingredient quantity to drop to zero or below, the corresponding `ShoppingListItem` is removed.
- If adding a meal introduces an ingredient not currently on the list, a new `ShoppingListItem` is added.
- Consolidation logic (basic, from Phase 7a) is applied during these updates.

## Implementation Details

### Database Schema
*(No changes needed from Phase 7a schema)*

### Models
*(No changes needed from Phase 7a schema)*

### Events (`App\Events\...`)
These events should be dispatched from the relevant controllers/services handling `MealAssignment` changes (e.g., `MealAssignmentController`, potentially a `MealAssignmentService`).

```php
<?php
// Example: Dispatched when a meal is assigned/updated
namespace App\Events;

use App\Models\MealAssignment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MealAssignmentChanged // Could be more specific (Created, Updated)
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public MealAssignment $mealAssignment, // Contains meal_plan_id, recipe_id, date, servings, to_cook
        public ?array $originalAttributes = null // Useful for updates to know what changed
    ) {}
}

<?php
// Example: Dispatched when a meal assignment is deleted
namespace App\Events;

// ... imports ...
class MealAssignmentDeleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $mealPlanId,
        public int $recipeId,
        public string $date, // Y-m-d format
        public int $servings, // Original servings
        public bool $wasToCook // Original to_cook status
    ) {}
}
```
*Note: These are examples. The exact event structure might be refined based on where they are dispatched.*

### Event Listeners (`App\Listeners\...`)

```php
<?php
namespace App\Listeners;

use App\Events\MealAssignmentChanged;
use App\Events\MealAssignmentDeleted;
use App\Services\ShoppingListUpdateService; // New or extended service
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateShoppingListOnMealChange implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private readonly ShoppingListUpdateService $shoppingListUpdateService
    ) {}

    public function handle(MealAssignmentChanged|MealAssignmentDeleted $event): void
    {
        if ($event instanceof MealAssignmentChanged) {
            $this->shoppingListUpdateService->handleAssignmentChange($event->mealAssignment, $event->originalAttributes);
        } elseif ($event instanceof MealAssignmentDeleted) {
            $this->shoppingListUpdateService->handleAssignmentDeletion(
                $event->mealPlanId,
                $event->recipeId,
                $event->date,
                $event->servings,
                $event->wasToCook
            );
        }
    }
}
```

### Service (`App\Services\ShoppingListUpdateService`)
This new service contains the logic to find and update shopping lists based on the events.

```php
<?php
namespace App\Services;

use App\Models\MealAssignment;
use App\Models\ShoppingList;
use App\Models\Recipe;
// ... other necessary imports

class ShoppingListUpdateService
{
    // Constructor potentially injecting IngredientRepository, RecipeRepository

    public function handleAssignmentChange(MealAssignment $assignment, ?array $originalAttributes): void
    {
        // Determine if the change affects shopping lists (e.g., date changed, to_cook changed, servings changed)
        $wasAffecting = $this->wasAffectingList($originalAttributes, $assignment->getOriginal('date'));
        $isAffecting = $this->isAffectingList($assignment);

        if (!$wasAffecting && !$isAffecting) return; // No relevant change

        $affectedLists = $this->findAffectedLists($assignment->meal_plan_id, $assignment->date);
        if ($affectedLists->isEmpty()) return;

        $recipe = Recipe::with('ingredients')->find($assignment->recipe_id);
        if (!$recipe) return; // Should not happen

        foreach ($affectedLists as $list) {
            if ($wasAffecting && !$isAffecting) { // Became irrelevant or removed from range
                $this->removeOrDecrementIngredients($list, $recipe, $this->getOriginalServings($originalAttributes));
            } elseif (!$wasAffecting && $isAffecting) { // Became relevant or added to range
                $this->addOrIncrementIngredients($list, $recipe, $assignment->servings);
            } elseif ($wasAffecting && $isAffecting) { // Relevant change (servings, maybe to_cook status edge case)
                $originalServings = $this->getOriginalServings($originalAttributes);
                if ($originalServings !== $assignment->servings) {
                    // Adjust quantities based on serving difference
                    $this->adjustIngredientQuantities($list, $recipe, $originalServings, $assignment->servings);
                }
            }
        }
    }

    public function handleAssignmentDeletion(int $mealPlanId, int $recipeId, string $date, int $servings, bool $wasToCook): void
    {
        if (!$wasToCook) return; // Only care if it was contributing

        $affectedLists = $this->findAffectedLists($mealPlanId, $date);
        if ($affectedLists->isEmpty()) return;

        $recipe = Recipe::with('ingredients')->find($recipeId);
        if (!$recipe) return;

        foreach ($affectedLists as $list) {
            $this->removeOrDecrementIngredients($list, $recipe, $servings);
        }
    }

    private function findAffectedLists(int $mealPlanId, string $date): Collection
    {
        // Find lists where date is between start_date and end_date
        // Potentially filter by meal_plan_id if we add that link later
        return ShoppingList::where('start_date', '<=', $date)
                           ->where('end_date', '>=', $date)
                           // ->where('meal_plan_id', $mealPlanId) // If linked
                           ->get();
    }

    private function isAffectingList(MealAssignment $assignment): bool
    {
        return $assignment->to_cook;
        // Add date range check if assignment could be updated outside a list's range
    }

    private function wasAffectingList(?array $originalAttributes, ?string $originalDate): bool
    {
        if (!$originalAttributes) return false; // Was a new assignment
        $originalToCook = $originalAttributes['to_cook'] ?? false;
        // Check if original date was within range if date could change
        return $originalToCook;
    }

    private function getOriginalServings(?array $originalAttributes): int
    {
        return $originalAttributes['servings'] ?? 0;
    }

    private function addOrIncrementIngredients(ShoppingList $list, Recipe $recipe, int $servings): void
    {
        // Logic to fetch recipe ingredients adjusted for servings
        // Iterate ingredients:
        //   Find existing ShoppingListItem (ingredient_id, unit)
        //   If exists, increment quantity
        //   If not exists, create new ShoppingListItem
    }

    private function removeOrDecrementIngredients(ShoppingList $list, Recipe $recipe, int $servings): void
    {
        // Logic to fetch recipe ingredients adjusted for servings
        // Iterate ingredients:
        //   Find existing ShoppingListItem (ingredient_id, unit)
        //   If exists, decrement quantity
        //   If quantity <= 0, delete ShoppingListItem
    }

    private function adjustIngredientQuantities(ShoppingList $list, Recipe $recipe, int $oldServings, int $newServings): void
    {
        // Calculate quantity difference per ingredient based on serving change
        // Iterate ingredients:
        //   Find existing ShoppingListItem
        //   Adjust quantity (add or subtract difference)
        //   If quantity <= 0, delete item
        //   If item didn't exist and difference is positive, create it (edge case?)
    }
}
```
*Note: The service logic requires careful implementation, especially around quantity adjustments and consolidation.* 

### Event Service Provider (`App\Providers\EventServiceProvider`)
Register the listener.
```php
protected $listen = [
    MealAssignmentChanged::class => [
        UpdateShoppingListOnMealChange::class,
    ],
    MealAssignmentDeleted::class => [
        UpdateShoppingListOnMealChange::class,
    ],
];
```

### User Interface
*(No direct UI changes for this phase)*
- Users will observe that their generated shopping lists automatically reflect changes made in the meal plan view (e.g., removing a meal, changing servings). There's no explicit UI control for synchronization.

## Testing Strategy

### Unit Tests
- Test `ShoppingListUpdateService` methods thoroughly:
    - `findAffectedLists` logic.
    - `isAffectingList` / `wasAffectingList` conditions.
    - Correct calculation of ingredient quantities based on servings.
    - Correctly adding/incrementing items (`addOrIncrementIngredients`).
    - Correctly removing/decrementing items (`removeOrDecrementIngredients`).
    - Correctly adjusting quantities (`adjustIngredientQuantities`).
    - Handling of edge cases (item not found, quantity becomes zero).
- Test Event/Listener registration.

### Integration Tests
- Test the full flow: Dispatch `MealAssignmentChanged` -> Listener triggers -> Service executes -> `ShoppingListItem` quantities/presence are updated correctly in the database.
- Test the full flow for `MealAssignmentDeleted`.
- Test scenarios:
    - Adding a new `to_cook` assignment within a list's range.
    - Changing an assignment's `to_cook` from false to true.
    - Changing an assignment's `to_cook` from true to false.
    - Deleting a `to_cook` assignment.
    - Increasing servings for a `to_cook` assignment.
    - Decreasing servings for a `to_cook` assignment.
    - Changes outside a list's date range should *not* affect the list.

## Future Considerations (Handled in subsequent phases)
- More robust unit conversion integrated into updates.
- UI notifications indicating the list has been updated automatically?
- Performance considerations for large meal plans/many lists. 