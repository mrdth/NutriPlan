# Composer Scripts

This document outlines the available composer scripts in the project, their purposes, and when to use them.

## Development Scripts

### `composer dev`
- **Purpose**: Starts the full development environment
- **When to use**: During active development
- **What it runs**:
  - Laravel development server
  - Queue listener
  - Log viewer (Pail)
  - Vite development server
- **Note**: Uses concurrently to run all services in parallel with color-coded output

### `composer dev:ssr`
- **Purpose**: Starts the development environment with Server-Side Rendering (SSR)
- **When to use**: When testing or developing with SSR enabled
- **What it runs**:
  - Builds SSR assets
  - Laravel development server
  - Queue listener
  - Log viewer
  - Inertia SSR server
- **Note**: Similar to `dev` but includes SSR support

## Testing Scripts

### `composer test:type-coverage`
- **Purpose**: Runs type coverage tests
- **When to use**: To verify type coverage meets minimum requirements
- **What it runs**: Pest with type coverage checking (minimum 100%)

### `composer test:lint`
- **Purpose**: Runs code style checks
- **When to use**: Before committing code to ensure consistent style
- **What it runs**:
  - Laravel Pint (PHP code style)
  - NPM format checking

### `composer test:unit`
- **Purpose**: Runs unit tests
- **When to use**: To verify application functionality
- **What it runs**: Pest with parallel execution and coverage reporting

### `composer test:types`
- **Purpose**: Runs static type analysis
- **When to use**: To catch type-related issues
- **What it runs**: PHPStan analysis

### `composer test:refactor`
- **Purpose**: Checks for potential code refactoring opportunities
- **When to use**: Before major refactoring to identify potential improvements
- **What it runs**: Rector in dry-run mode

### `composer test` or `composer tests`
- **Purpose**: Runs all test suites
- **When to use**: Before deploying or when you want to run all checks
- **What it runs**:
  - Type coverage tests
  - Unit tests
  - Linting
  - Refactoring checks

## Code Quality Scripts

### `composer lint`
- **Purpose**: Formats and lints code
- **When to use**: To automatically fix code style issues
- **What it runs**:
  - Laravel Pint
  - NPM formatting
  - NPM linting

### `composer refactor`
- **Purpose**: Automatically refactors code
- **When to use**: To apply automated code improvements
- **What it runs**: Rector with default rules

## Post-Install/Update Scripts

These run automatically at specific times:

### `post-autoload-dump`
- **When**: After composer autoloader is dumped
- **Purpose**: Discovers Laravel packages

### `post-update-cmd`
- **When**: After composer update
- **Purpose**: Publishes Laravel assets

### `post-root-package-install`
- **When**: After initial project installation
- **Purpose**: Sets up initial project files

### `post-create-project-cmd`
- **When**: After creating a new project
- **Purpose**: Initializes the project with:
  - Generates application key
  - Creates SQLite database
  - Runs initial migrations 