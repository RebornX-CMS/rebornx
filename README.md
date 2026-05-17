# RebornX CMS

**A modernized fork of XOOPS 2.0.5**

RebornX is a content management system (CMS) based on XOOPS Version 2.0.5 (October 8, 2003). This project aims to bring the classic XOOPS codebase into the modern era with comprehensive updates including PHP 8.2+ compatibility, modern database drivers, a new theming system, true internationalization (i18n), and a host of new features — while preserving the modular architecture that made XOOPS popular.

---

## Features

### Core Modernization
- **PHP 8.2+ Compatible** — Codebase updated to run on modern PHP versions with full type safety and removal of deprecated constructs.
- **MySQLi / PDO** — Replaced the original `mysql_*` functions with modern MySQLi and PDO database abstraction layers for improved security, performance, and compatibility.
- **Error Handling** — Robust exception-based error handling replacing legacy PHP error mechanisms.

### Theming
- **New Theming System** — A redesigned theme architecture separating logic from presentation, supporting template inheritance and modern CSS/JS asset management.
- **Smarty Templating** — Continued use of the Smarty template engine, updated to the latest stable version for security and performance.
- **Responsive by Default** — All core themes are built with responsive design principles.

### Internationalization
- **True i18n** — Complete rewrite of the language/translation system supporting proper locale handling, pluralization, character encoding (UTF-8 everywhere), and date/number formatting.
- **Multi-Language Content** — Built-in support for serving content in multiple languages.

### Database
- **MySQLi Support** — Full MySQLi driver with prepared statements for query parameterization, eliminating SQL injection vectors.
- **PDO Support** — Optional PDO abstraction allowing database-agnostic operation (MySQL, PostgreSQL, SQLite, etc.).
- **Migration Tools** — Upgrade path from the original XOOPS 2.0.x database schema.

### Security
- **XSS Prevention** — Modern output encoding and sanitization throughout the system.
- **CSRF Protection** — Token-based cross-site request forgery protection on all forms.
- **Password Hashing** — bcrypt/argon2 password hashing replacing the original MD5-based system.
- **Session Management** — Improved session handling with secure, configurable storage backends.

### Modules (Pre-packaged)
| Module | Description |
|--------|-------------|
| Contact | Contact form with customizable fields and email routing |
| myDownloads | File/software download manager with categories and ratings |
| myLinks | Web link directory with categories, ratings, and hit tracking |
| NewBB | Discussion forum with threads, polls, and private messaging |
| News | Article/announcement publishing with categories and approval workflow |
| Sections | Hierarchical content organization for structured pages |
| System | Core system administration, preferences, blocks, and user management |
| XoopsFAQ | Frequently Asked Questions manager with categorized entries |
| XoopsHeadline | RSS/feed aggregator for displaying external headlines |
| XoopsMembers | Member directory with search, sorting, and profile display |
| XoopsPartners | Partner/affiliate link management with logo display |
| XoopsPoll | Poll and survey creation with real-time results |

---

## Requirements

- **Web Server:** Apache 2.4+ with `mod_rewrite`, or Nginx
- **PHP:** 8.2 or higher
- **Database:** MySQL 5.7+ / MariaDB 10.3+ (MySQLi or PDO)
- **Extensions:** `mbstring`, `gd`/`imagick`, `json`, `session`, `filter`, `ctype`, `tokenizer`
- **Browser:** Modern browser with JavaScript enabled

## Installation

1. **Download** the latest release from the repository.
2. **Extract** the archive to your web server's document root (e.g., `/var/www/html/rebornx`).
3. **Create a database** and user with full privileges:
   ```sql
   CREATE DATABASE rebornx CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'rebornx'@'localhost' IDENTIFIED BY 'your_password';
   GRANT ALL PRIVILEGES ON rebornx.* TO 'rebornx'@'localhost';
   FLUSH PRIVILEGES;
   ```
4. **Copy** `mainfile.dist.php` to `mainfile.php` and adjust database credentials and paths.
5. **Point your browser** to `http://your-server/rebornx/install/` and follow the on-screen installer.
6. **Remove or secure** the `install/` directory after installation.

## Upgrading from XOOPS 2.0.x

1. **Back up** your existing database and files.
2. **Replace** all files with the RebornX distribution (preserving `mainfile.php` and `uploads/`).
3. **Run** the upgrade script at `http://your-server/rebornx/upgrade/`.
4. **Review** the admin panel for any configuration adjustments needed under the new system.

---

## Directory Structure

```
rebornx/
├── admin.php              # Administration panel entry point
├── backend.php            # Backend services / cron replacement
├── banners.php            # Banner management
├── edituser.php           # User profile editing
├── footer.php             # Global footer include
├── header.php             # Global header include
├── image.php              # Image display handler
├── imagemanager.php       # Image management
├── index.php              # Site entry point
├── lostpass.php           # Password recovery
├── mainfile.dist.php      # Distribution configuration template
├── mainfile.php           # Active site configuration
├── misc.php               # Miscellaneous handlers
├── notifications.php      # Notification settings
├── pda.php                # PDA/mobile view
├── pmlite.php             # Lightweight private messaging
├── readpmsg.php           # Read private messages
├── register.php           # User registration
├── robots.txt             # Search engine directives
├── search.php             # Site search
├── user.php               # User account management
├── userinfo.php           # Public user profile
├── viewpmsg.php           # View private messages
├── xmlrpc.php             # XML-RPC interface
├── xoops.css              # Global stylesheet
│
├── cache/                 # Cache storage (writable)
├── class/                 # Core class libraries
│   ├── database/          # Database abstraction layer
│   ├── mail/              # Email handling
│   ├── smarty/            # Smarty template engine
│   ├── xml/               # XML handling
│   └── xoopsform/         # Form generation library
├── include/               # Core includes and libraries
├── install/               # Installation wizard
├── kernel/                # Kernel/object classes
├── language/              # Language files
├── modules/               # Extensible modules
├── templates_c/           # Compiled templates (writable)
├── themes/                # Theme files
├── upgrade/               # Database/file upgrade scripts
└── uploads/               # User uploads (writable)
```

---

## Development

### Building and Contributing

1. Fork the repository.
2. Create a feature branch: `git checkout -b feature/my-feature`.
3. Commit your changes with clear, descriptive messages.
4. Push to your branch: `git push origin feature/my-feature`.
5. Submit a pull request.

### Coding Standards

- PSR-12 for PHP code.
- Follow existing patterns in the kernel and module classes.
- Document all public methods with PHPDoc blocks.
- Use prepared statements for all database queries.
- Escape all output with `htmlspecialchars()` or the provided sanitization utilities.

---

## License

**RebornX CMS** is free software distributed under the terms of the **GNU General Public License v3.0 (GPLv3)**.

```
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
```

See the [LICENSE](./LICENSE) file for the complete text.

### Attribution

RebornX CMS is based on [XOOPS](https://www.xoops.org/) Version 2.0.5, copyright © 2000-2003 XOOPS.org.  
XOOPS is released under the GNU General Public License.

---

*Maintained by the RebornX CMS project contributors.*
