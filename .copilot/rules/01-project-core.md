
---
name: Jivident Project Core
alwaysApply: true
---

# Jivident - Core Development Rules

You are working on Jivident, a clinic management application built with Laravel and NativePHP Desktop.

## General Principles

- Always inspect the existing codebase before implementing changes.
- Understand existing architecture, conventions, and business logic.
- Prefer modifying existing implementations over creating duplicate systems.
- Never assume a feature, table, route, or service exists without checking.
- Never modify unrelated modules.
- Preserve existing functionality and user workflows.
- Prioritize maintainability, reliability, and simplicity.
- Avoid unnecessary dependencies and overengineering.

## Development Workflow

Before implementing any feature:

1. Inspect relevant files and dependencies.
2. Understand the current implementation.
3. Identify potential impacts on other modules.
4. Present a concise implementation plan for complex changes.
5. Implement incrementally.
6. Run relevant tests and validation.
7. Report modified files and remaining issues.

## Coding Standards

- Follow existing project conventions.
- Use clear and descriptive naming.
- Keep methods focused and maintainable.
- Avoid duplicated logic.
- Handle exceptions properly.
- Validate all external inputs.
- Never expose credentials or sensitive information.
- Do not silently swallow errors.

## Important Restrictions

- Do not delete existing data.
- Do not run destructive database commands without explicit approval.
- Never use migrate:fresh or db:wipe on existing databases.
- Do not overwrite existing configuration without understanding its purpose.
- Do not introduce breaking changes without explaining them.
- Do not claim tests passed unless they were actually executed.

## Communication

- Explain important architectural decisions.
- Mention potential regressions before making risky changes.
- Prefer practical solutions over unnecessary abstractions.
- Clearly distinguish implemented features from proposed improvements.
