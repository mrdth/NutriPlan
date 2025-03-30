# Meal Planning - Phase 4: Drag and Drop + "To Cook" Flags

## Overview
This phase enhances the meal planning interface by implementing a drag-and-drop system for recipe organization and introduces "to cook" flags to help users identify which meals need preparation.

## Core Functionality

### Drag and Drop Interface
- Users can drag recipes between days in the meal plan
- Visual cues indicate valid drop zones during drag operations
- Smooth animations provide feedback during drag and drop
- Implementation works for both desktop and mobile interfaces

### "To Cook" Flagging
- First occurrence of each recipe in the plan is automatically flagged as "to cook"
- Users can manually toggle the "to cook" flag for any recipe occurrence
- Visual indicators clearly distinguish recipes flagged for cooking
- Count of "to cook" items is displayed per day for quick reference

### Enhanced Calendar View
- More sophisticated calendar-style interface
- Improved visual design for day containers
- Better spacing and organization of recipes within days
- Recipe cards with enhanced information display

## Database Structure

### MealPlanRecipe Model Updates
- Add to_cook field (boolean, default true for first occurrence)
- Maintain existing day, scaling, and relationship fields

## Implementation Details

### Backend Requirements
- Migration to add to_cook field to meal_plan_recipe table
- API endpoints for drag-and-drop operations
- Logic to automatically flag first occurrences as "to cook"
- Methods for toggling "to cook" status

### Frontend Requirements
- Drag-and-drop library integration (e.g., vue-draggable)
- Enhanced recipe card design with "to cook" toggle
- Visual indicators for drag-and-drop interactions
- Refined calendar layout with improved spacing and organization
- Accessibility considerations for drag and drop

## Mobile Considerations
- Touch-based drag and drop implementation
- Larger drag handles for mobile interfaces
- "Long press" gesture to initiate drag on touch devices
- Haptic feedback during drag operations (where supported)

## Testing Criteria
- Drag and drop operations work correctly across devices
- "To cook" flags are correctly applied to first occurrences
- Manual toggling of "to cook" status works properly
- Enhanced calendar view renders correctly on various screen sizes
- Accessibility requirements are met for all interactions

## Value Proposition
This phase significantly improves the user experience of meal planning by making recipe organization more intuitive through drag and drop. The "to cook" flagging system helps users track which meals they need to prepare, adding practical value to the meal planning process. 