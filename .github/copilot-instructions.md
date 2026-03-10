# Copilot Instructions for Ludophylosophie Backend

## Project Overview
- This is a Laravel-based backend for Ludophylosophie, structured as a monolithic application with RESTful APIs.
- Main business logic resides in `app/Http/Controllers/`, with models in `app/Models/` and policies in `app/Policies/`.
- Data flows from HTTP requests (via routes in `routes/`) to controllers, which interact with Eloquent models and return JSON responses.
- Media files for thematiques are managed via Laravel's Storage API, typically stored in `storage/app/public/thematique` and exposed via `/storage/thematique/` URLs.

## Key Workflows
- **Run the application:** Use `php artisan serve` to start the local server.
- **Database migrations:** Use `php artisan migrate` to apply schema changes. SQL seeders and factories are in `database/`.
- **Testing:** Run `php artisan test` for PHPUnit tests (see `tests/`).
- **Debugging:** Use Laravel's built-in logging (`\Log::error`) and exception handling. Errors are returned as JSON via custom response helpers.

## Project-Specific Patterns
- **API Responses:** Standardized via `PackageControlleur::successResponse` and `PackageControlleur::errorResponse` (see controllers for usage).
- **Media Handling:** Only main thematiques (no parent) can have media files/URLs. Sub-thematiques have media fields set to null.
- **Soft Deletes:** Deletion of thematiques also soft-deletes related questions and badges. Sub-thematiques are deleted recursively.
- **Validation:** Uses Laravel's Validator, but with custom rules for media and parent relationships.
- **Custom Naming:** Some models (e.g., `badge_users.php`) use snake_case filenames, but class names are PascalCase.

## Integration Points
- **External Storage:** Uses Laravel's Storage facade for file uploads.
- **Authentication:** Configured via `config/auth.php` and policies in `app/Policies/`.
- **Frontend:** Likely integrated via API endpoints defined in `routes/api.php`.

## Conventions & Examples
- Controllers return JSON with status codes and custom messages.
- Media uploads are handled in controller methods (`store`, `update`) with file validation and public URL generation.
- Example: See `ThematiqueController::store` for file upload and validation logic.

## Directory References
- `app/Http/Controllers/` — Main API logic
- `app/Models/` — Eloquent models
- `database/migrations/` — Schema definitions
- `routes/api.php` — API route definitions
- `storage/app/public/thematique/` — Media file storage

---
_If any section is unclear or missing important project-specific details, please provide feedback to improve these instructions._
