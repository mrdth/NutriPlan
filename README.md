# NutriPlan

NutriPlan is a modern recipe management and meal planning application built with Laravel, Vue.js, and Inertia.js. It allows users to collect, organize, and plan their recipes efficiently.

## Features

- Recipe management with support for ingredients, categories, and cooking instructions
- Import recipes from other websites via JSON-LD data
- User authentication and recipe ownership
- Recipe status management (draft, published, archived)
- Standardized measurements and unit conversions
- SEO-friendly URLs with automatic slug generation
- Modern, responsive UI built with Tailwind CSS

## Tech Stack

- **Backend Framework**: Laravel 10.x
- **Frontend Framework**: Vue.js 3.x with TypeScript
- **Full-stack Framework**: Inertia.js
- **CSS Framework**: Tailwind CSS
- **Testing**: Pest PHP
- **Static Analysis**: PHPStan
- **Code Style**: Laravel Pint
- **Database**: SQLite (default), supports MySQL/PostgreSQL

## Requirements

- PHP 8.4 or higher
- Node.js 18.x or higher
- Composer 2.x
- SQLite (or MySQL/PostgreSQL if preferred)

## Local Development Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/nutriplan.git
   cd nutriplan
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install Node.js dependencies:
   ```bash
   npm install
   ```

4. Create your environment file:
   ```bash
   cp .env.example .env
   ```

5. Generate application key:
   ```bash
   php artisan key:generate
   ```

6. Create the SQLite database:
   ```bash
   touch database/database.sqlite
   ```

7. Run database migrations:
   ```bash
   php artisan migrate
   ```

8. Start the development server:
   ```bash
   # In one terminal:
   php artisan serve

   # In another terminal:
   npm run dev
   ```

The application will be available at `http://localhost:8000`.

## Testing

Run the test suite:
```bash
php artisan test
```

Run static analysis:
```bash
./vendor/bin/phpstan analyse
```

Fix code style:
```bash
./vendor/bin/pint
```

## Database Structure

### Key Models

- **Recipe**: Core model for storing recipes
  - Belongs to a User
  - Has many Ingredients through RecipeIngredient pivot
  - Belongs to many Categories
  - Includes fields for title, description, instructions, timing, and source data

- **Category**: For organizing recipes
  - Has many Recipes
  - Can be active/inactive

- **Ingredient**: For recipe components
  - Belongs to many Recipes through RecipeIngredient pivot
  - Can be marked as common/uncommon
  - Uses standardized measurements

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework for Web Artisans
- [Vue.js](https://vuejs.org) - The Progressive JavaScript Framework
- [Inertia.js](https://inertiajs.com) - The Modern Monolith
- [Tailwind CSS](https://tailwindcss.com) - A utility-first CSS framework
- [Spatie](https://spatie.be) - For their excellent Laravel packages
