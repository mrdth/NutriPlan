# Meal Planning - Phase 1: Basic Meal Plans

## Overview
This phase introduces the foundational structure for meal planning, allowing users to create and manage empty meal plans with basic metadata.

## Core Functionality

### Meal Plan Creation
- Users can create meal plans for 1 or 2 weeks
- When creating a plan, users specify:
  - Start date for the plan
  - Number of people the plan is for
  - Optional name for the plan

### Meal Plan Management
- Users can view a list of all their saved meal plans
- Plans display:
  - Plan name (if provided)
  - Start and end dates
  - Number of people
  - Creation date
- Users can delete unwanted meal plans

## Database Structure

### MealPlan Model
- User relationship (belongs to a user)
- Name (optional string)
- Start date (date)
- Duration (integer, 7 or 14 days)
- Number of people (integer)
- Created date (automatic timestamp)

## Implementation Details

### Backend Requirements
- Migration file for meal_plans table
- MealPlan model with validation rules
- MealPlanController with basic CRUD operations
- API routes for meal plan operations

### Frontend Requirements
- Meal Plans index page listing all user plans
- Create form with appropriate inputs:
  - Date picker for start date
  - Number input for people
  - Text input for optional name
- Basic styling consistent with application design
- Delete confirmation modal

## Testing Criteria
- Users can successfully create meal plans
- Input validation works correctly for all fields
- Plans appear correctly in the index listing
- Plans can be successfully deleted
- Appropriate error handling for all operations

## Value Proposition
This phase gives users the ability to start organizing their meal planning structure, even before recipe integration is implemented. It establishes the foundation for all future meal planning phases. 