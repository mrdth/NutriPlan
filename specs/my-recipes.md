# My Recipes Feature

## Overview
The My Recipes feature allows users to toggle between viewing all recipes in the system and only their own recipes on the recipes index page. This feature helps users quickly find and manage their personal recipe contributions.

## User Story
As a user, I want to be able to filter the recipes index page to show only recipes I have added, so that I can easily manage and find my own recipes.

## Functional Requirements

### Toggle Button
- Add a toggle button/switch in the recipes index page header
- Button states:
  - "All Recipes" (default)
  - "My Recipes"
- The selected state should be visually indicated
- The state should persist across page reloads using session storage

### Recipe Filtering
- When "My Recipes" is selected:
  - Only show recipes where the user_id matches the current authenticated user
  - Update the recipe count to reflect only the user's recipes
- When "All Recipes" is selected:
  - Show all recipes in the system
  - Update the recipe count to show total recipes

### UI/UX Requirements
- The toggle should be prominently placed in the page header, before the 'Import Recipe' button
- Use consistent styling with the existing UI components
- Provide smooth transition animation when switching between views
- Include a loading state while filtering

## Technical Specifications

### Backend Changes
- Add a new route parameter to handle the filter state
- Modify the recipe index controller to accept a `show_mine` parameter
- Update the recipe query to filter by user_id when required
- Add appropriate tests for the new filtering functionality
- Return appropriate Inertia responses with the filtered data

### Frontend Changes
- Create new Vue component for the toggle button using radix-vue
- Implement state management using Vue's reactive system
- Use Inertia.js visit() for handling filter state changes
- Update the recipe list component to handle filtered results
- Persist filter preference in localStorage

### Database Changes
- No database changes required (utilizing existing user_id relationship)

## Security Considerations
- Ensure proper authentication checks are in place
- Validate user permissions before showing personal recipes
- Sanitize and validate all user inputs

## Testing Requirements
- Unit tests for the controller modifications
- Feature tests for the filtering functionality
- Frontend component tests for the toggle button
- Integration tests for the complete feature

## Acceptance Criteria
1. Users can successfully toggle between "All Recipes" and "My Recipes" views
2. The correct recipes are displayed based on the selected filter
3. The toggle state persists across page reloads
4. The feature works correctly with proper error handling
5. All security measures are properly implemented
6. The UI is responsive and provides appropriate feedback

## Implementation Notes
- Use existing authentication mechanisms
- Follow established Vue/Inertia patterns and standards
- Maintain consistent error handling
- Document any new Vue components
- Update relevant documentation 