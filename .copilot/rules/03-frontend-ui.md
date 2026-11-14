
---
name: Jivident Frontend Standards
globs: ["resources/views/**/*.blade.php", "resources/js/**/*.js", "resources/css/**/*.css"]
---

# Frontend Development Rules

- Follow the existing Jivident UI design and conventions.
- Prefer Blade templates for server-rendered interfaces.
- Prefer existing jQuery patterns when working with legacy modules.
- Reuse existing Bootstrap components.
- Use DataTables for tabular data when already established in the module.
- Use SweetAlert for confirmation and notification when already available.
- Avoid introducing React, Vue, or another frontend framework unless explicitly requested.
- Keep JavaScript modular and avoid unnecessary global variables.
- Handle loading, empty, success, and error states.
- Ensure responsive layouts.
- Maintain accessibility and consistent form validation.
- Avoid unnecessary npm dependencies.
- Do not redesign existing screens unless requested.
