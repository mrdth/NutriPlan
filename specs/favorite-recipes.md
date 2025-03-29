# Favorite Recipes Feature Specification

## Overview
The favorite recipes feature allows users to mark recipes as favorites and provides a dedicated interface to view and manage their favorite recipes.

## Database Changes

### New Migration
Create a new migration for the favorites pivot table:

```php
Schema::create('favorites', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
    $table->timestamps();
    
    $table->unique(['user_id', 'recipe_id']);
});
```

### Model Updates

#### User Model
Add the following relationship:
```php
public function favorites()
{
    return $this->belongsToMany(Recipe::class, 'favorites')
                ->withTimestamps();
}
```

#### Recipe Model
Add the following relationship:
```php
public function favoritedBy()
{
    return $this->belongsToMany(User::class, 'favorites')
                ->withTimestamps();
}
```

## API Endpoints

### FavoriteController
Create a new controller with the following methods:

#### toggle
- **Route**: `POST /recipes/{recipe}/favorite`
- **Method**: `__invoke(Recipe $recipe)`
- **Description**: Toggles the favorite status of a recipe for the current user
- **Response**: 
  - Success: `200 OK` with updated favorite status
  - Error: `404 Not Found` if recipe doesn't exist

## Frontend Components

### RecipeCard Component Updates
Add a favorite button to the dropdown menu:
- Position: Above the "Add to Collection" button
- Icon: Use `Heart` icon from Lucide icons
- State: 
  - Filled heart when favorited
  - Outline heart when not favorited
- Action: Toggle favorite status via Inertia form submit

### Recipe Show Page Updates
Add a favorite button:
- Position: Near the recipe title
- Style: Match existing UI
- State: 
  - Filled heart when favorited
  - Outline heart when not favorited
- Action: Toggle favorite status via Inertia form submit

### New Favorites Page
Create a new page at `/favorites`:
- Layout: Similar to recipes index page
- Content:
  - Page title: "My Favorite Recipes"
  - Grid of recipe cards showing only favorited recipes
  - Empty state message when no favorites exist
- Navigation: Add link in sidebar menu

## Testing Requirements

### Unit Tests
- Test User model favorites relationship
- Test Recipe model favoritedBy relationship
- Test FavoriteController toggle method

### Feature Tests
- Test favorite/unfavorite functionality
- Test favorites page display
- Test empty state handling
- Test unauthorized access

### Browser Tests
- Test favorite button interactions
- Test favorites page navigation
- Test responsive design

## UI/UX Considerations
- Provide visual feedback when toggling favorite status
- Ensure consistent icon usage across the application
- Maintain accessibility standards
- Consider adding animations for state changes
- Implement proper loading states during API calls

## Security Considerations
- Ensure only authenticated users can favorite recipes
- Validate all user input
- Implement proper authorization checks
- Protect against CSRF attacks

## Performance Considerations
- Optimize database queries for favorites
- Implement proper caching strategies
- Consider pagination for favorites list
- Optimize API response times

## Future Enhancements
- Add favorite count to recipe cards
- Implement favorite sorting options
- Add favorite categories/tags
- Enable bulk favorite operations 