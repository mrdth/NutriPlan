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
4. [ ] Update categories on recipe edit page
  - [ ] Update the `RecipeForm` component to show only selected categories
    - Clicking a category chip should remove it from the list of selected categories
    - All categories should still be passed to the front end
  - [ ] Add a searchable select for categories. 
    - Selected categories should be pre-selected
    - Selecting a category should add it to the list of selected categories
    - Unselecting a category should remove it from the list of selected categories
5. [ ] Filtering Categories
  - [ ] Add a filter to the categories page to filter the list of categories.
    - [ ] The filter should be a text input field.
    - [ ] The filter should be debounced to prevent too many requests.
    - [ ] The filter should filter the list of categories.
      - [ ] The filter should be case-insensitive.
      - [ ] The filter should be fuzzy.
6. [ ] Implement Collections
  - [ ] Create a new page to show the collections for the user.
    - [ ] Add a 'New Collection' button. (the same as the 'New Recipe' button on the Recipes.Index page)
      - [ ] Display a modal with a form to create a new collection.
        - The form should have fields for the collection name and description.
        - The form should have a submit button.
        - The form should have a cancel button.
    - [ ] Show the collections as a list.
      - Each item in the list should have:
        - The collection name
        - The description
        - The number of recipes in the collection
        - A link to the collection
          - We can reuse the recipes.index page, but with a filtered list of recipes.
    - [ ] Add a link to the collections page from the navigation menu in the sidebar.
  - [ ] Update the `Recipe.Show` page to show the collections the current user has added this recipe to.
7. [ ] Add nutrition information for recipes.
  - [ ] We'll use nutrition information based on the schema.org format: https://schema.org/NutritionInformation
  - [ ] Create models and migrations needed to store nutrition information and associate it with recipes.
  - [ ] Update the `RecipeParser` to extract nutrition information from the recipe source.
  - [ ] Update the `RecipeForm` component to allow input / editing of nutrition information.
    - Make all fields optional.
    - Use the schema.org format for the fields.
    - Place the nutrition information fields in a separate section of the form, in a collapsible panel.
  - Use the `NutritionInformation` component to display the information on the recipe page.
