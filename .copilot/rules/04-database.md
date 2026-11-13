
---
name: Jivident Database Standards
globs: ["app/**/*.php", "database/**/*.php", "config/database.php"]
---

# Database Architecture Rules

- Inspect the existing schema and migration history before making changes.
- Preserve existing data and database compatibility.
- Prioritize SQLite and MySQL compatibility.
- Avoid database-specific SQL unless necessary.
- Use Laravel Schema Builder where practical.
- Use migrations for schema changes.
- Never assume a database connection is available during application bootstrap.
- Avoid database queries inside service provider boot methods unless safely guarded.
- Never automatically execute migrate:fresh or db:wipe.
- Never delete or overwrite an existing database during configuration changes.
- Use transactions for operations requiring atomicity.
- Consider offline operation when implementing database-dependent features.
- Handle missing connections and unavailable databases gracefully.
- Validate schema compatibility before switching databases.
- Keep database configuration independent from application data.
