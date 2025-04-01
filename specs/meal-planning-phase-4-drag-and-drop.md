# Meal Planning - Phase 4: Drag and Drop + "To Cook" Flags

## Overview
This phase enhances the meal planning interface by implementing a drag-and-drop system for meal assignment organization and introduces "to cook" flags to help users identify which specific meal instances need preparation.

## Core Functionality

### Drag and Drop Interface
- Users can drag meal assignments between days in the meal plan
- Visual cues indicate valid drop zones during drag operations
- Smooth animations provide feedback during drag and drop
- Implementation works for both desktop and mobile interfaces

### "To Cook" Flagging
- First meal assignment created from a MealPlanRecipe entry is automatically flagged as "to cook"
- Users can manually toggle the "to cook" flag for any meal assignment
- Visual indicators clearly distinguish meal assignments flagged for cooking
- Count of "to cook" items is displayed per day for quick reference

### Enhanced Calendar View
- More sophisticated calendar-style interface
- Improved visual design for day containers
- Better spacing and organization of meal assignments within days
- Meal assignment cards with enhanced information display (recipe name, serving count, "to cook" toggle)

## Database Structure

### MealAssignment Model Updates (from Phase 3)
- Add `to_cook` field (boolean, default based on automatic flagging logic)
- Maintain existing relationships and `servings_count` field

## Implementation Details

### Backend Requirements
- Migration to add `to_cook` field to `meal_assignments` table
- API endpoints for drag-and-drop operations (moving meal assignments)
- Logic to automatically flag the first created meal assignment from a `MealPlanRecipe` as "to cook"
- Methods for toggling `to_cook` status on `MealAssignment`

### Frontend Requirements
- Drag-and-drop library integration (e.g., vue-draggable)
- Enhanced meal assignment card design with "to cook" toggle
- Visual indicators for drag-and-drop interactions
- Refined calendar layout with improved spacing and organization
- Accessibility considerations for drag and drop

## Mobile Considerations
- Touch-based drag and drop implementation
- Larger drag handles for mobile interfaces
- "Long press" gesture to initiate drag on touch devices
- Haptic feedback during drag operations (where supported)

## Testing Criteria
- Drag and drop operations for meal assignments work correctly across devices
- "To cook" flags are correctly applied to the first meal assignment
- Manual toggling of "to cook" status works properly
- Enhanced calendar view renders correctly on various screen sizes
- Accessibility requirements are met for all interactions

## Value Proposition
This phase significantly improves the user experience of meal planning by making meal assignment organization more intuitive through drag and drop. The "to cook" flagging system helps users track exactly which meal instances they need to prepare, adding practical value to the meal planning process. 