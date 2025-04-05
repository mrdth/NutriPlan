# Meal Planning - Phase 7: Shopping List Integration

## Overview
This phase will add the ability to automatically generate shopping lists from meal plans, helping users efficiently plan their grocery shopping based on the recipes they've scheduled.

## Core Functionality

### Shopping List Generation
- Automatic extraction of ingredient information from recipes in the meal plan
- Consolidation of ingredients into a comprehensive shopping list
- Intelligent combining of like ingredients with quantity calculations
- Support for different measurement units

### Shopping List Management
- View, edit and delete shopping lists
- Mark items as purchased/acquired
- Add custom items not derived from recipes
- Sort ingredients by categories or custom order

### Integration with Meal Planning
- Generate shopping list for entire meal plan or selected days
- Update shopping list when recipes are added/removed from plan
    - Use Laravel's event system to handle this
- Include only "to cook" recipes in shopping list
    - Recipes added to the meal plan, but not yet assigned to a day should not be included
- Shopping lists should automatically update when:
    - A meal is assigned to a day AND marked as "to cook"
    - A meal's "to cook" status changes
    - A meal is removed from a day
    - A meal's serving size is adjusted

## Implementation Details

### Database Schema

```sql
-- Shopping Lists
CREATE TABLE shopping_lists (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Shopping List Items
CREATE TABLE shopping_list_items (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shopping_list_id BIGINT UNSIGNED NOT NULL,
    ingredient_id BIGINT UNSIGNED,
    name VARCHAR(255) NOT NULL,
    quantity DECIMAL(8,2),
    unit VARCHAR(50),
    category VARCHAR(100),
    is_custom BOOLEAN DEFAULT FALSE,
    is_purchased BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (shopping_list_id) REFERENCES shopping_lists(id),
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id)
);
```

### Models

#### ShoppingList Model
```php
class ShoppingList extends Model
{
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_purchased' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(ShoppingListItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

#### ShoppingListItem Model
```php
class ShoppingListItem extends Model
{
    protected $casts = [
        'quantity' => 'decimal:2',
        'is_custom' => 'boolean',
        'is_purchased' => 'boolean',
    ];

    public function shoppingList()
    {
        return $this->belongsTo(ShoppingList::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
```

### Events
```php
// Events that trigger shopping list updates
class MealAssignedToDay
    - Properties: meal_plan_id, recipe_id, date, to_cook, servings
class MealToCookStatusChanged
    - Properties: meal_plan_id, recipe_id, date, to_cook
class MealRemovedFromDay
    - Properties: meal_plan_id, recipe_id, date
class MealServingsChanged
    - Properties: meal_plan_id, recipe_id, date, old_servings, new_servings
```

### Event Handlers
```php
class UpdateShoppingListForMealChanges
{
    // Handles:
    // - MealAssignedToDay
    // - MealToCookStatusChanged
    // - MealRemovedFromDay
    // - MealServingsChanged
    
    // Responsibilities:
    // - Find associated shopping list(s) for the meal plan
    // - Update ingredient quantities based on the event
    // - Add or remove ingredients as needed
    // - Trigger shopping list update notifications
}
```

### Controllers

#### ShoppingListController
Handles the core CRUD operations for shopping lists.

```php
class ShoppingListController extends Controller
{
    public function __construct(
        private readonly ShoppingListService $shoppingListService
    ) {}

    /**
     * Display a listing of the user's shopping lists
     * GET /shopping-lists
     */
    public function index(): ResourceCollection

    /**
     * Display the specified shopping list
     * GET /shopping-lists/{shoppingList}
     */
    public function show(ShoppingList $shoppingList): ShoppingListResource

    /**
     * Store a new shopping list
     * POST /shopping-lists
     */
    public function store(StoreShoppingListRequest $request): JsonResponse

    /**
     * Update shopping list details
     * PUT /shopping-lists/{shoppingList}
     */
    public function update(UpdateShoppingListRequest $request, ShoppingList $shoppingList): ShoppingListResource

    /**
     * Delete the shopping list
     * DELETE /shopping-lists/{shoppingList}
     */
    public function destroy(ShoppingList $shoppingList): JsonResponse
}
```

#### ShoppingListGenerationController
Handles the generation of shopping lists from meal plans.

```php
class ShoppingListGenerationController extends Controller
{
    public function __construct(
        private readonly ShoppingListService $shoppingListService
    ) {}

    /**
     * Generate a shopping list from a meal plan.
     * Automatically generates a name like "Shopping List for [Meal Plan Name]"
     * which can be overridden by the user via the request or edited later.
     * POST /meal-plans/{mealPlan}/shopping-list
     */
    public function store(GenerateShoppingListRequest $request, MealPlan $mealPlan): ShoppingListResource

    /**
     * Regenerate/refresh an existing shopping list
     * POST /shopping-lists/{shoppingList}/regenerate
     */
    public function update(ShoppingList $shoppingList): ShoppingListResource
}
```

#### ShoppingListItemController
Handles operations on individual shopping list items.

```php
class ShoppingListItemController extends Controller
{
    public function __construct(
        private readonly ShoppingListService $shoppingListService
    ) {}

    /**
     * Add a custom item to the shopping list
     * POST /shopping-lists/{shoppingList}/items
     */
    public function store(StoreShoppingListItemRequest $request, ShoppingList $shoppingList): ShoppingListItemResource

    /**
     * Update a shopping list item
     * PUT /shopping-lists/{shoppingList}/items/{item}
     */
    public function update(UpdateShoppingListItemRequest $request, ShoppingList $shoppingList, ShoppingListItem $item): ShoppingListItemResource

    /**
     * Remove an item from the shopping list
     * DELETE /shopping-lists/{shoppingList}/items/{item}
     */
    public function destroy(ShoppingList $shoppingList, ShoppingListItem $item): JsonResponse
}
```

#### ShoppingListItemPurchaseController
Handles the purchase status of shopping list items.

```php
class ShoppingListItemPurchaseController extends Controller
{
    public function __construct(
        private readonly ShoppingListService $shoppingListService
    ) {}

    /**
     * Toggle the purchased status of an item
     * POST /shopping-lists/{shoppingList}/items/{item}/toggle-purchased
     */
    public function store(ShoppingList $shoppingList, ShoppingListItem $item): ShoppingListItemResource

    /**
     * Mark multiple items as purchased/unpurchased
     * POST /shopping-lists/{shoppingList}/items/bulk-purchase
     */
    public function bulkUpdate(BulkPurchaseRequest $request, ShoppingList $shoppingList): JsonResponse
}
```

### Form Requests

#### StoreShoppingListRequest
```php
class StoreShoppingListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
```

#### UpdateShoppingListRequest
```php
class UpdateShoppingListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
```

#### GenerateShoppingListRequest
```php
class GenerateShoppingListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'include_uncategorized' => ['boolean'],
        ];
    }
}
```

#### StoreShoppingListItemRequest
```php
class StoreShoppingListItemRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'category' => ['nullable', 'string', 'max:100'],
        ];
    }
}
```

#### BulkPurchaseRequest
```php
class BulkPurchaseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'items' => ['required', 'array'],
            'items.*' => ['required', 'exists:shopping_list_items,id'],
            'purchased' => ['required', 'boolean'],
        ];
    }
}
```

### Services

#### ShoppingListService
- generateFromMealPlan() - Create list from meal plan
- consolidateIngredients() - Combine like ingredients
- convertUnits() - Handle unit conversions
- addCustomItem() - Add non-recipe item
- updateItemStatus() - Update purchase status

### Unit Conversion

The system will support basic unit conversions for common measurements:
- Volume (ml, l, cups, tbsp, tsp)
- Weight (g, kg, oz, lb)
- Count/Pieces

A conversion matrix will be maintained for supported unit types.

### User Interface

#### Views/Components
- Shopping Lists Index (`/shopping-lists`)
- Shopping List Detail (`/shopping-lists/{shoppingList}`)
- Shopping List Creation Modal
- Item Categories Management (Future?)

#### Features
- Drag and drop reordering of items
- Quick purchase toggle
- Category filtering/sorting
- Unit conversion display (if applicable)
- Progress indicator for purchased items
- Clear empty state on index page encouraging list creation

#### Creation Workflows

**1. From Meal Plan View (`/meal-plans/{mealPlan}`):**
   - **Trigger:** "Generate Shopping List" button.
   - **UI:** Modal opens.
   - **Form Fields:**
     - Indication of the source Meal Plan.
     - `name`: Text input, pre-filled (e.g., "List for [Plan Name] - Full Plan"), editable.
     - `period`: Select list ("Full Plan", "Week 1", "Week 2" - options depend on plan duration).
     - Optional checkboxes (e.g., `include_uncategorized`).
   - **Action:** Submit triggers `ShoppingListGenerationController::store`.
   - **Feedback:** Modal closes, success notification.

**2. From Shopping List Index View (`/shopping-lists`):**
   - **Trigger:** "Create New Shopping List" button.
   - **UI:** Modal opens.
   - **Initial Form Fields:**
     - `list_type`: Select list ("Create Empty List", "Generate from Meal Plan").
   - **Dynamic Fields (based on `list_type`):**
     - **If "Empty List":**
       - `name`: Required text input.
       - **Action:** Submit triggers `ShoppingListController::store`.
     - **If "Generate from Meal Plan":**
       - `meal_plan_id`: Required select/search for Meal Plan.
       - `name`: Text input, pre-filled based on selected plan and period, editable.
       - `period`: Select list ("Full Plan", "Week 1", "Week 2" - options depend on selected plan duration).
       - Optional checkboxes.
       - **Action:** Submit triggers `ShoppingListGenerationController::store`.
   - **Feedback:** Modal closes, success notification.

## Testing Strategy

### Unit Tests
- Test ingredient consolidation logic
- Test unit conversion accuracy
- Test shopping list generation
- Test custom item addition

### Feature Tests
- Test shopping list CRUD operations
- Test item management
- Test meal plan integration
- Test purchase status updates

### Integration Tests
- Test event handling
- Test automatic list updates
- Test unit conversion system

## Future Considerations
- Ingredient categorization by grocery department
- Integration with external shopping apps
- Saving favorite or recurring grocery items
- Intelligent suggestions based on past shopping patterns

*This specification will be expanded with implementation details prior to development.* 