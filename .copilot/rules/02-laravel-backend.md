
---
name: Laravel Backend Standards
globs: ["app/**/*.php", "routes/**/*.php", "database/**/*.php", "config/**/*.php"]
---

# Laravel Backend Rules

- Follow the existing Laravel version and project architecture.
- Use Laravel conventions and built-in features whenever practical.
- Keep controllers focused on HTTP handling.
- Put reusable business logic into appropriate services.
- Use Form Requests when validation complexity justifies them.
- Use Eloquent ORM following existing project patterns.
- Use database transactions for multi-step operations that must be atomic.
- Handle exceptions and provide meaningful error messages.
- Prevent SQL injection and mass assignment vulnerabilities.
- Avoid N+1 queries where possible.
- Never expose internal exception details to end users.
- Check existing migrations before creating new ones.
- Keep migrations reversible when practical.
- Preserve compatibility with supported database drivers.
- Never run destructive migrations without explicit approval.
