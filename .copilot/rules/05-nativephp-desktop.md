
---
name: NativePHP Desktop Standards
globs: ["app/**/*.php", "config/**/*.php", "resources/views/**/*.blade.php"]
---

# NativePHP Desktop Rules

- Treat Jivident as a desktop-first application with offline capabilities.
- Inspect the installed NativePHP version and its documentation before using APIs.
- Do not assume browser-only APIs are available in desktop runtime.
- Use NativePHP filesystem and application data facilities appropriately.
- Keep persistent user data outside the application installation directory.
- Do not hardcode absolute filesystem paths.
- Consider application startup and shutdown lifecycle.
- Handle unavailable network connections gracefully.
- Do not require internet access for local SQLite operations.
- Test behavior in both development and packaged builds when possible.
- Avoid conflicting with NativePHP automatic database initialization.
- Keep desktop-specific functionality isolated where practical.
