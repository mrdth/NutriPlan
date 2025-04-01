# Meal Planning - Phase 3: Day Organization

## Overview
This phase extends the meal planning functionality by introducing the concept of "meals" as distributable units, allowing users to assign recipe servings to specific days within their meal plan.

## Core Functionality

### Meal Tracking
- When a recipe is added to a meal plan, available "meals" are calculated:
  - Available meals = (Recipe servings ÷ Number of people in plan)
  - Example: A 4-serving recipe added to a plan for 2 people creates 2 available meals
- Available meals are tracked and displayed for each recipe in the plan

### Day Assignment
- Users can assign available meals to specific days within the meal plan duration
- Users can see how many meals are available/remaining for each recipe
- Users can move meal assignments between days using simple dropdown selectors

### Day-Based View
- Meal plan detail page is organized to display meals by day
- Each day shows:
  - Day name and date
  - List of assigned meals (showing recipe name and serving count)
  - Option to add more meals to that day

## Database Structure

### MealPlanRecipe Model
- Maintain existing relationships from Phase 2
- Add servings_available field (decimal, calculated when recipe is added)

### MealPlanDay Model (New)
- MealPlan relationship (foreign key)
- Day number (integer, representing day number from plan start date)
- Timestamps for creation/update

### MealAssignment Model (New)
- MealPlanDay relationship (foreign key)
- MealPlanRecipe relationship (foreign key)
- Servings count (decimal, defaults to 1.0)
- Timestamps for creation/update

## Implementation Details

### Backend Requirements
- Migrations for new tables (meal_plan_days, meal_assignments)
- Models for new entities
- Controller methods to handle meal assignments and movements
- API routes for meal assignment operations

### Frontend Requirements
- Simple calendar-style layout for meal plan detail page
- Day headers with dates calculated from plan start date
- For each recipe in the meal plan:
  - Display of available meals count
  - Simple dropdown selector for assigning meals to days
  - Button to remove meal assignments
- For each day:
  - List of assigned meals with recipe names and serving counts
  - Button to add more meals with day pre-selected
- Visual indicators for remaining available meals (e.g., counter or progress bar)

## Testing Criteria
- Correct calculation of available meals when recipes are added
- Meals can be successfully assigned to specific days
- Available meal count decreases when meals are assigned
- Moving meals between days works correctly
- Days display correct date based on plan start date
- All previous functionality continues to work

## Value Proposition
This phase transforms meal plans into practical daily meal schedules while solving the problem of recipe repetition. Users can efficiently distribute recipe servings across multiple days without redundant recipe entries, creating a more intuitive and flexible meal planning experience. 