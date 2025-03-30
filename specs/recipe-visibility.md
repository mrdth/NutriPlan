# Recipe Visibility Feature

## Overview
The Recipe Visibility feature allows users to control who can view their recipes. By default, recipes are private and visible only to their creator. Users can choose to make recipes public, allowing other users to view them. Additionally, imported recipes from external websites have special visibility rules to respect original content.

## User Story
As a recipe creator, I want to control who can view my recipes, so that I can keep personal recipes private while sharing others with the community. Additionally, when I import recipes from other sites, I want to ensure proper attribution to the original source.

## Functional Requirements

### Recipe Privacy Controls
- Add a visibility toggle on recipe creation/edit forms
- Toggle states:
  - "Private" (default)
  - "Public"
- The selected state should be visually indicated
- Provide clear explanation of what each option means

### Visibility Rules
- Private recipes:
  - Only visible to the creator of the recipe
  - Not included in general recipe listings for other users
- Public recipes:
  - Visible to all users in recipe listings
  - Full details visible for non-imported recipes
  - Limited details visible for imported recipes (see below)

### Special Handling for Imported Recipes
- When a recipe is imported (has a URL field):
  - Owner can always see full recipe details
  - Other users viewing public imported recipes:
    - Can see basic info (title, description, images, cooking time)
    - Cannot see ingredients and instructions
    - Are shown a prominent "View Original Recipe" button
    - See attribution to the original source

### UI/UX Requirements
- Visibility toggle should be clearly visible on recipe forms
- Visual indicators in recipe listings to show public/private status
- For public imported recipes viewed by non-owners:
  - Placeholder content where ingredients/instructions would normally appear
  - Clear messaging about why content is hidden
  - Prominent, styled "View Original Recipe" button

## Technical Specifications

### Database Changes
- Add `is_public` boolean column to the `recipes` table (default: false)

### Backend Changes
- Update Recipe model with:
  - `is_public` attribute in fillable array
  - Helper method: `isImported(): bool`
  - Appropriate scopes for visibility filtering
- Modify RecipePolicy to enforce visibility rules
- Update controllers to respect visibility settings
- Add tests for new visibility logic

### Frontend Changes
- Add visibility toggle component to recipe forms
- Update recipe listing to include visibility status indicators
- Create "View Original Recipe" component for imported public recipes
- Implement conditional rendering based on recipe visibility and ownership

### API Changes
- Ensure API endpoints respect visibility rules
- Update serialization to include visibility status for owners

## Security Considerations
- Validate visibility permissions on all routes and API endpoints
- Prevent unauthorized access to private recipes
- Ensure recipe visibility can only be changed by the owner

## Testing Requirements
- Unit tests for visibility model attributes and methods
- Policy tests for visibility rule enforcement
- Controller tests for visibility filtering
- Frontend component tests for visibility toggle
- Integration tests for the complete feature

## Acceptance Criteria
1. All new recipes are private by default
2. Recipe owners can toggle their recipes between private and public
3. Private recipes are only visible to their owners
4. Public non-imported recipes show full details to all users
5. Public imported recipes show limited details to non-owners with original source link
6. The visibility settings persist correctly
7. All UI elements clearly indicate visibility status
8. Proper attribution is shown for imported recipes

## Implementation Notes
- Use existing authentication and authorization mechanisms
- Consider performance impacts of additional visibility filtering
- Provide clear documentation for users about visibility controls
- Update existing tests to accommodate new visibility rules 