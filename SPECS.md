# Project Specifications

This document provides an overview of all feature specifications for the NutriPlan project.

## Feature Specifications

| Feature | Description | Status | Specification |
|---------|-------------|---------|---------------|
| Core Recipe Management | Create, view, edit, and list recipes with CRUD functionality | ✅ | [View Spec](specs/core-recipe-management.md) |
| Recipe Import & Enhancement | Import recipes from external sites with automatic parsing | ✅ | [View Spec](specs/recipe-import.md) |
| Collections & Categories | Organize recipes with categories and custom collections | ✅ | [View Spec](specs/collections-categories.md) |
| Recipe Functionality | Recipe scaling, ingredient management, and optional units | ✅ | [View Spec](specs/recipe-functionality.md) |
| Favorite Recipes | Allow users to mark recipes as favorites and view their favorite recipes | ✅ | [View Spec](specs/favorite-recipes.md) |
| Recipe Deletion | Allow users to delete their own recipes | ✅ | [View Spec](specs/recipe-deletion.md) |
| My Recipes | Toggle to filter between all recipes and user's own recipes | ✅ | [View Spec](specs/my-recipes.md) |
| Recipe Visibility | Control recipe privacy and visibility with special handling for imported recipes | ✅ | [View Spec](specs/recipe-visibility.md) |
| User Recipe Filtering | Filter recipes by specific user, showing user profiles and replacing My Recipes toggle | ✅ | [View Spec](specs/user-recipe-filtering.md) |
| Meal Planning | Create and manage meal plans with recipes | ⏳ | [View Spec](specs/meal-planning.md) |

### Meal Planning Phases

| Phase | Description | Status | Specification |
|---------|-------------|---------|---------------|
| Phase 1: Basic Meal Plans | Create and manage empty meal plans with basic metadata | ✅ | [View Spec](specs/meal-planning-phase-1-basic-meal-plans.md) |
| Phase 2: Recipe Assignment | Add recipes to meal plans and manage servings | ✅ | [View Spec](specs/meal-planning-phase-2-recipe-assignment.md) |
| Phase 3: Day Organization | Assign recipes to specific days within the plan | ⏳ | [View Spec](specs/meal-planning-phase-3-day-organization.md) |
| Phase 4: Drag and Drop + "To Cook" Flags | Implement drag-and-drop interface and cooking flags | ⏳ | [View Spec](specs/meal-planning-phase-4-drag-and-drop.md) |
| Phase 5: Plan Copying | Enable copying existing plans to create new ones | ⏳ | [View Spec](specs/meal-planning-phase-5-plan-copying.md) |
| Phase 6: Mobile Optimization | Fully optimize the experience for mobile devices | ⏳ | [View Spec](specs/meal-planning-phase-6-mobile-optimization.md) |
| Phase 7: Shopping List Integration | Automatically generate shopping lists from meal plans | 🔮 | [View Spec](specs/meal-planning-phase-7-shopping-list-integration.md) |
| Phase 8: Nutritional Summaries | Show nutritional totals per day or week | 🔮 | [View Spec](specs/meal-planning-phase-8-nutritional-summaries.md) |
| Phase 9: Cooking Notifications | Reminders for meals flagged "to cook" | 🔮 | [View Spec](specs/meal-planning-phase-9-cooking-notifications.md) |

## Status Legend
- ✅ Complete
- 🚧 In Progress
- ⏳ Planned
- 🔮 Future Consideration 