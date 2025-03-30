# Meal Planning - Phase 2: Recipe Assignment

## Overview
This phase builds upon the basic meal plan structure by enabling users to add recipes to their meal plans. The focus is on simple addition of recipes without day-specific organization.

## Core Functionality

### Recipe Assignment
- Users can add recipes to a meal plan:
  - From a recipe detail page via an "Add to Meal Plan" dropdown
  - From within the meal plan via search functionality
- Each recipe added includes:
  - Reference to the original recipe
  - Number of servings based on the plan's number of people

### Recipe Management in Plans
- Users can view all recipes added to a meal plan
- Users can remove recipes from a meal plan
- Users can view recipe details directly from the meal plan

### Recipe Servings Calculation
- Leverage existing recipe scaling functionality
- Initial servings calculation based on:
  - Original recipe servings ÷ Number of people in the plan
- Meals available is calculated and displayed based on this ratio

## Database Structure

### MealPlanRecipe Model (Pivot)
- MealPlan relationship (foreign key)
- Recipe relationship (foreign key)
- Scale factor (decimal, defaults to 1.0)
- Timestamps for creation/update

## Implementation Details

### Backend Requirements
- Migration file for meal_plan_recipe pivot table
- MealPlanRecipe model for the pivot relationship
- Controller methods for adding/removing recipes
- API routes for recipe assignment operations

### Frontend Requirements
- Update Recipe detail page with "Add to Meal Plan" dropdown
- Update Meal Plan detail page with:
  - Recipe search functionality
  - List of assigned recipes
  - Remove recipe buttons
- Recipe scaling display to show available meals

## Integration Points
- Utilize existing recipe scaling component/functionality from recipe detail page
- Ensure consistency with existing recipe display patterns

## Testing Criteria
- Users can add recipes from both recipe detail and meal plan pages
- Correct calculation of servings based on plan details
- Successful removal of recipes from plans
- Proper display of all recipe information

## Value Proposition
This phase enables users to begin populating their meal plans with recipes, creating a useful collection of meals for their planning period. It establishes the recipe-plan relationship that will be further organized in subsequent phases. 