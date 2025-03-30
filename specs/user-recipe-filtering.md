# User Recipe Filtering

## Overview
The User Recipe Filtering feature allows users to view recipes created by a specific user. This enhances discoverability and community engagement by making it easier to find recipes from users whose cooking style you enjoy, while respecting recipe visibility settings. This feature will replace the current "My Recipes" toggle functionality with a more comprehensive user-based filtering approach.

## User Story
As a user, I want to see recipes created by a specific user, so I can explore more content from creators whose recipes I enjoy. Additionally, I want easy access to all my own recipes through my profile page.

## Functional Requirements

### User Profile URL Structure
- Each user should have a unique, slug-based URL for their recipe listing
- URL format: `/recipes/by/{user-slug}`
- Slugs should be automatically generated from usernames
- Slugs should be unique and handle special characters appropriately

### Recipe Filtering by User
- Filter the recipe index page to show recipes from a specific user
- When viewing another user's profile: only show their public recipes
- When viewing your own profile: show all your recipes (both public and private)
- Display the targeted user's name prominently on the filtered page
- Maintain existing sorting and pagination functionality
- Filter via URL parameter for direct linking

### Navigation Between Views
- When viewing a user's recipe list, display an "All Recipes" button in the same position as the current toggle
- "All Recipes" button should navigate back to the main recipe index
- The user's profile link in the navigation should be active when viewing their recipes
- Provide clear visual indication of which view is currently active

### Empty State Handling
- Display an appropriate message when a user has no visible recipes
- Provide helpful navigation options from the empty state
- Ensure the message is user-friendly and encourages content discovery
- Different messages for own empty profile vs. another user's empty profile

### User Attribution Links
- Add clickable links to usernames throughout the application
- Recipe detail pages should link the creator's name to their public recipe listing
- Links should be styled consistently with the application design
- Hover states should indicate the link functionality

### Replacement of "My Recipes" Toggle
- Remove the current "My Recipes" toggle from the UI
- Replace with a prominent link to the user's own profile in the navigation
    - Should be styled as a button, the same as current toggle
- When on a user's recipe page, display an "All Recipes" button where the toggle currently is
    - Should use consistent styling with the current toggle
    - Should link back to the main recipe index page
- Ensure the transition is intuitive for existing users
- Update all related UI elements and documentation

## Technical Specifications

### Database Structure
- Add sluggable functionality to the User model (See Recipe model for example)
- Ensure proper indexing for optimized queries
- Add scopes for retrieving recipes by user with visibility control

### Backend Implementation
- Update RecipeController to handle user filtering
- Add route for user recipe listing
- Implement proper authorization and visibility checks
- Create policy for viewing user's recipes
- Optimize queries for performance

### Frontend Components
- User recipe listing page with appropriate header
- Empty state component for users with no recipes
- Update username displays to be clickable links
- Loading states for asynchronous data loading
- Update navigation to include direct link to own profile
- "All Recipes" button component for returning to main index

## Security Considerations
- Ensure only appropriate recipes are displayed when filtering by user
- Prevent enumeration attacks through proper error handling
- Validate user slugs to prevent injection attacks
- Apply appropriate rate limiting to prevent abuse
- Ensure proper authorization checks for viewing private recipes

## Testing Requirements
- Unit tests for User model sluggable functionality
- Feature tests for recipe filtering by user (both own and others)
- Tests for empty state handling
- Integration tests for user attribution links
- Tests for authorization rules (own vs others' recipes)
- Tests for navigation between filtered and unfiltered views

## Acceptance Criteria
1. Users can view all public recipes from other users via a slug-based URL
2. Users can view all their own recipes (public and private) when viewing their own profile
3. An appropriate message is displayed when a user has no visible recipes
4. Username links throughout the application correctly navigate to the user's recipe listing
5. The filtered view respects all existing sorting and pagination functionality
6. The feature works correctly for users with special characters in their names
7. The UI updates appropriately to replace the "My Recipes" toggle with profile navigation
8. When viewing a user's recipe list, an "All Recipes" button is available to return to the main recipe index

## Implementation Notes
- Ensure backward compatibility with existing routes
- Consider performance impacts on recipe listing queries
- Use eager loading to minimize database queries
- Follow existing application patterns for URL structure and filtering
- Provide clear UI guidance during the transition from "My Recipes" toggle to profile-based filtering 