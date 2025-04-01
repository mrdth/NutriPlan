# Meal Planning - Phase 3a: Meal Tracking

## Overview
This phase extends the meal planning functionality by introducing the concept of "meals" as distributable units, calculating and tracking available meals for recipes added to a meal plan.

## Core Functionality

### Meal Tracking
- When a recipe is added to a meal plan, available "meals" are calculated:
  - Available meals = (Recipe servings ÷ Number of people in plan)
  - Example: A 4-serving recipe added to a plan for 2 people creates 2 available meals
- Available meals are tracked and displayed for each recipe in the plan

## Database Structure

### MealPlanRecipe Model
- Maintain existing relationships from Phase 2
- Add servings_available field (decimal, calculated when recipe is added)

## Implementation Details

### Backend Requirements
- Migration to add servings_available to meal_plan_recipes table
- Update MealPlanRecipe model with new field
- Update controller methods to calculate servings_available when recipes are added
- API routes to retrieve available meals data

### Frontend Requirements
- Keep the ability to add a recipe to the meal plan via search functionality
- Update meal plan detail page to display available meals for each recipe
- Visual indicators for available meals (e.g., counter or progress bar)

## Testing Criteria
- Correct calculation of available meals when recipes are added
- Available meals count is displayed accurately in the UI
- All previous functionality continues to work

## Value Proposition
This phase lays the foundation for practical meal distribution by introducing the concept of "available meals" for each recipe. Users will be able to see how many meals they can create from each recipe based on serving size and plan participants, providing better planning visibility. 