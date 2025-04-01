# Meal Planning - Phase 3c: Meal Assignments

## Overview
This phase completes the meal planning functionality by implementing the ability to assign recipe servings to specific days, creating a comprehensive daily meal schedule.

## Core Functionality

### Day Assignment
- Users can assign available meals to specific days within the meal plan duration
- Users can see how many meals are available/remaining for each recipe
- Users can move meal assignments between days using simple dropdown selectors

### Meal Management
- For each day, users can view all assigned meals
- Users can remove meal assignments, returning servings to the available pool
- Available meals count is updated in real-time as assignments are made/removed

## Database Structure

### MealAssignment Model (New)
- MealPlanDay relationship (foreign key)
- MealPlanRecipe relationship (foreign key)
- Servings count (decimal, defaults to 1.0)
- Timestamps for creation/update

## Implementation Details

### Backend Requirements
- Migration for new table (meal_assignments)
- MealAssignment model creation
- Controller methods to handle meal assignments and movements
- API routes for meal assignment operations
- Logic to update available servings when assignments change

### Frontend Requirements
- UI for assigning meals to days (dropdown or drag-and-drop interface)
- For each recipe in the meal plan:
  - Display of remaining available meals count
  - Simple selector for assigning meals to days
  - Button to remove meal assignments
- For each day:
  - List of assigned meals with recipe names and serving counts
  - Button to add more meals with day pre-selected

## Testing Criteria
- Meals can be successfully assigned to specific days
- Available meal count decreases when meals are assigned
- Available meal count increases when assignments are removed
- Moving meals between days works correctly
- All previous functionality continues to work

## Value Proposition
This phase completes the practical meal planning experience by allowing users to distribute recipe servings across multiple days without redundant recipe entries. Users can now create a full weekly meal schedule with precise serving counts, providing a complete solution for planning meals efficiently and reducing food waste. 