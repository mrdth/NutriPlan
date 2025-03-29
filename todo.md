1. [x] Update recipe card component.
  - [x] Show the full description of the recipe on the card, instead of truncating it.
  - [x] Show only the 3 most popular categories for the recipe on the card.
    - A category's popularity is determined by the number of recipes it is used on.
  - [x] If the recipe was imported (has a URL), show the sitename as a chip on the card.
    - The sitename is the domain of the URL, excluding the protocol and subdomain.
    - The chip should link to the URL.
2. [x] Allow ingredient unit to be null.
  - [x] Update the `unit` column in the `ingredient_recipe` table to allow null values.
  - [x] Update the `RecipeForm` component to allow null values.
  - [x] Update the form requests to allow null values.
3. [x] Visualizing Categories
  - [x] Create a new page to show the categories.
    - Use some kind of word cloud or tag cloud to visualize the categories.
    - The size of each word should be proportional to the number of recipes it is used on.
    - Clicking on a word should navigate to the recipes that use that category.
      - We can reuse the recipes.index page, but with a filtered list of recipes.
  - [x] Add a link to the categories page from the navigation menu in the sidebar.
  - [x] Update the `RecipeCard` component to link the category chips to the filtered recipes page.
  - [x] Update the `Recipe.Show` page to link the category chips to the filtered recipes page.
4. [x] Update categories on recipe edit page
  - [x] Update the `RecipeForm` component to show only selected categories
    - Clicking a category chip should remove it from the list of selected categories
    - All categories should still be passed to the front end
  - [x] Add a searchable select for categories. 
    - Selected categories should be pre-selected
    - Selecting a category should add it to the list of selected categories
    - Unselecting a category should remove it from the list of selected categories
5. [x] Improved Ingredient Input
  - Currently when adding / updating a recipe, the ingredient input is a select, allowing only existing ingredients to be selected.
  - [x] Allow new ingredients to be added.
6. [x] Filtering Categories
  - [x] Add a filter to the categories page to filter the list of categories.
    - [x] The filter should be a text input field.
    - [x] The filter should be debounced to prevent too many requests.
    - [x] The filter should filter the list of categories.
      - [x] The filter should be case-insensitive.
      - [x] The filter should be fuzzy.
7. [x] Implement Collections
  - [x] Create a new page to show the collections for the user.
    - [x] Add a 'New Collection' button. (the same as the 'New Recipe' button on the Recipes.Index page)
      - [x] Display a modal with a form to create a new collection.
        - The form should have fields for the collection name and description.
        - The form should have a submit button.
        - The form should have a cancel button.
    - [x] Show the collections as a list.
      - Each item in the list should have:
        - The collection name
        - The description
        - The number of recipes in the collection
        - A link to the collection
          - We can reuse the recipes.index page, but with a filtered list of recipes.
    - [x] Add a link to the collections page from the navigation menu in the sidebar.
  - [x] Update the `Recipe.Show` page to show the collections the current user has added this recipe to.
  - [x] Update the `RecipeCard` component to allow adding a recipe to a collection.
    - Add a menu to the bottom right of the card.
    - Add a button to the menu to add the recipe to a collection.
      - The menu should be triggered by a click on the button, which should be the 'ellipsis-vertical' icon.
      - [x] Display a modal with a form to add the recipe to a collection.
        - The form should have a select for the collection.
        - The form should have a submit button.
8. [x] Implement recipe scaling feature.
  - [x] Enhance the Measurement value object with a scale() method to handle scaling calculations.
    - [x] Ensure proper handling of different unit types (weight, volume, count).
    - [x] Implement smart rounding to avoid awkward measurements.
    - [x] Add comprehensive tests for the scaling functionality.
  - [x] Add a scaling control on the Recipe Show page that allows users to adjust the number of servings.
    - [x] Create a new Vue component with +/- buttons and input field to adjust servings.
    - [x] Implement reactive state management to track the scaling factor.
  - [x] Update the ingredient list display to use the scaling factor.
  - [x] Add a visual indicator when a recipe has been scaled.
  - [x] Add a "Reset to Original" button to revert to the original recipe quantities.

9. [x] Add nutrition information for recipes.
  - [x] We'll use nutrition information based on the schema.org format: https://schema.org/NutritionInformation
  - [x] Create models and migrations needed to store nutrition information and associate it with recipes.
  - [x] Update the `RecipeParser` to extract nutrition information from the recipe source.
  - [x] Update the `RecipeForm` component to allow input / editing of nutrition information.
    - Make all fields optional.
    - Use the schema.org format for the fields.
    - Place the nutrition information fields in a separate section of the form, in a collapsible panel.
  - [x] Use the `NutritionInformation` component to display the information on the recipe page.

10. [x] Implement favorite recipes feature.
  - [x] Create a many-to-many relationship between users and recipes for favorites.
    - [x] Add a migration to create the favorites pivot table.
    - [x] Update the User model with a favorites relationship method.
    - [x] Update the Recipe model with a favoritedBy relationship method.
  - [x] Create a FavoriteController with toggle functionality.
  - [x] Add a favorite/unfavorite button to dropdown menu of recipe cards, above the "Add to Collection" button.
  - [x] Add a favorite/unfavorite button to recipe detail page.
  - [x] Create a dedicated "Favorites" page to display all favorited recipes.
  - [x] Add tests for the favorite functionality.

11. [ ] Implement recipe deletion feature.
  - [ ] Backend Components
    - [ ] Add a `destroy` method to the `RecipeController` if it doesn't already exist
    - [ ] Create a `DeleteRecipeAction` class in `app/Actions`
    - [ ] Update or create `RecipePolicy` to include a `delete` method
  - [ ] Frontend Components
    - [ ] Add a delete button to the recipe edit form
    - [ ] Create a confirmation modal component or use an existing one
    - [ ] Add a delete method to recipe service/API client
  - [ ] Testing
    - [ ] Add controller tests in `tests/Feature/Http/Controllers/RecipeControllerTest.php`
    - [ ] Create action tests in `tests/Unit/Actions/DeleteRecipeActionTest.php`
    - [ ] Test policy in `tests/Unit/Policies/RecipePolicyTest.php`
    - [ ] Add frontend component tests

12. [ ] Implement My Recipes feature.
  - [ ] Backend Components
    - [ ] Update `RecipeController@index` to accept `show_mine` parameter
    - [ ] Add filter scope to Recipe model for user's recipes
    - [ ] Add tests for new filtering functionality
  - [ ] Frontend Components
    - [ ] Create toggle button Vue component
    - [ ] Update Recipes Index page to include toggle
    - [ ] Add state management for filter preference
  - [ ] Testing
    - [ ] Add controller filter tests
    - [ ] Add frontend component tests
    - [ ] Test filter persistence across page reloads
