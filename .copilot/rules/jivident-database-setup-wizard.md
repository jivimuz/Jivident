---
globs: '["app/**/*", "config/**/*", "resources/views/**/*", "routes/**/*",
  "database/**/*"]'
description: Core rules for implementing the database setup wizard and runtime
  database switching for Jivident NativePHP desktop application
---

Implement database setup wizard with SQLite/MySQL support, runtime switching, and offline capability. Configuration stored in NativePHP app data directory, not in application database. Laravel must boot without database connection.