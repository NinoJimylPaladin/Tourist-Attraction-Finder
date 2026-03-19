# Workspace Rules for PHP and MySQL (Cline)

## 1. Environment Setup

- Use PHP version 8.x or higher.
- Use MySQL 5.7+ or MariaDB equivalent.
- Configure a local development environment (XAMPP, Laragon, Docker, etc.).
- Keep environment variables in a `.env` file.
- Never commit `.env` or sensitive configuration files.

## 2. Project Structure

- Follow a consistent folder structure:
  - `/app` (core logic)
  - `/controllers`
  - `/models`
  - `/views`
  - `/config`
  - `/public`

- Entry point must be inside `/public` (e.g., index.php).
- Keep business logic out of views.

## 3. Database Connection Rules

- Use PDO for database connections.
- Centralize DB connection in a single file/class.
- Use environment variables for DB credentials.
- Do not create multiple connections unnecessarily.

## 4. Coding Workflow

- Create a feature branch before development.
- Write small, testable functions.
- Test code locally before committing.
- Follow consistent formatting (PSR-12 recommended).

## 5. Query Implementation

- Use prepared statements only.
- Store reusable queries inside model classes.
- Avoid inline SQL in controllers.
- Use transactions for critical operations.

## 6. File Naming Conventions

- Controllers: `UserController.php`
- Models: `User.php`
- Views: lowercase with hyphens (e.g., `user-profile.php`)
- Config files: lowercase (e.g., `database.php`)

## 7. Security in Workspace

- Sanitize all incoming data.
- Validate inputs at controller level.
- Escape output in views.
- Protect against CSRF (use tokens).

## 8. Debugging & Logging

- Enable error reporting in development only.
- Use logs instead of echo/var_dump in production.
- Store logs in `/storage/logs` or similar directory.

## 9. Dependency Management

- Use Composer for PHP dependencies.
- Keep `composer.json` updated.
- Run `composer install` after pulling changes.

## 10. Collaboration Rules

- Pull latest changes before starting work.
- Resolve conflicts carefully.
- Write clear commit messages.
- Review code before merging.

## 11. Testing Rules

- Manually test all endpoints and database actions.
- Use sample data for testing.
- Avoid testing on production database.

## 12. Deployment Awareness

- Keep development and production configs separate.
- Never hardcode URLs or credentials.
- Clean up debug code before deployment.

---

These workspace rules define how development should be performed within the Cline environment for PHP and MySQL projects.
