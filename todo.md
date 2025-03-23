1. Update recipe card component.
  - [x] Show the full description of the recipe on the card, instead of truncating it.
  - [x] Show only the 3 most popular categories for the recipe on the card.
    - A category's popularity is determined by the number of recipes it is used on.
  - [x] If the recipe was imported (has a URL), show the sitename as a chip on the card.
    - The sitename is the domain of the URL, excluding the protocol and subdomain.
    - The chip should link to the URL.
2. Allow ingredient unit to be null.
  - [ ] Update the `unit` column in the `ingredient_recipe` table to allow null values.
  - [ ] Update the `RecipeForm` component to allow null values.
  - [ ] Update the form requests to allow null values.
3. Add nutrition information for recipes.
  - [ ] We'll use nutrition information based on the schema.org format: https://schema.org/NutritionInformation
  - [ ] Create models and migrations needed to store nutrition information and associate it with recipes.
  - [ ] Update the `RecipeParser` to extract nutrition information from the recipe source.
  - [ ] Update the `RecipeForm` component to allow input / editing of nutrition information.
    - Make all fields optional.
    - Use the schema.org format for the fields.
    - Place the nutrition information fields in a separate section of the form, in a collapsible panel.
  - Use the `NutritionInformation` component to display the information on the recipe page.
