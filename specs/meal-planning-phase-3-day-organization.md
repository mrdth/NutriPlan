# Meal Planning - Phase 3: Day Organization

## Overview
This phase extends the meal planning functionality by allowing users to assign recipes to specific days within their meal plan, creating a structured weekly or bi-weekly meal schedule.

## Core Functionality

### Day Assignment
- Users can assign recipes to specific days within the meal plan duration
- When adding a recipe, users can specify which day(s) to add it to
- Users can move recipes between days using a simple day selector

### Day-Based View
- Meal plan detail page is reorganized to display recipes by day
- Each day shows:
  - Day name and date
  - List of assigned recipes
  - Option to add more recipes to that day

### Multiple Occurrences
- Recipes can appear on multiple days within a plan
- Each occurrence is tracked separately (allowing for future enhancements like different scaling per day)

## Database Structure

### MealPlanRecipe Model Updates
- Add day field (integer, representing day number from plan start date)
- Maintain existing relationships and scaling information

## Implementation Details

### Backend Requirements
- Migration to add day field to meal_plan_recipe table
- Update controller methods to handle day assignments
- Add methods for moving recipes between days

### Frontend Requirements
- Calendar-style layout for meal plan detail page
- Day headers with dates calculated from plan start date
- Interface for selecting days when adding recipes
- Simple controls for moving recipes between days
- Maintain recipe scaling functionality

## Testing Criteria
- Recipes can be successfully assigned to specific days
- Days display correct date based on plan start date
- Recipes appear under correct day assignments
- Moving recipes between days works correctly
- All previous functionality (adding/removing recipes, scaling) continues to work

## Value Proposition
This phase transforms meal plans from simple recipe collections into organized daily meal schedules. Users can now plan which days they'll prepare specific recipes, creating a more useful and practical meal planning experience. 