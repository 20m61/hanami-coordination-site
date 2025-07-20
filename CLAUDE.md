# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Hanami Coordination Site (花見調整サイト) is a PHP web application for organizing cherry blossom viewing events. It's a custom MVC framework built with modern PHP (8.0+) that allows users to create events, coordinate schedules and locations, manage items lists, and communicate via chat without requiring login.

## Key Commands

### Development Setup
```bash
# Install PHP dependencies
composer install

# Copy environment configuration
cp .env.example .env
# Edit .env to configure database and Pusher settings

# Set up database
mysql -u your_username -p < database/schema.sql
mysql -u your_username -p < database/sample_data.sql  # Optional: sample data
```

### Testing & Quality Control
```bash
# Run PHPUnit tests
composer test

# Run PHPStan static analysis
composer phpstan

# Run PHP CodeSniffer
composer phpcs
```

### Database Management
```bash
# Create database and schema
mysql -u your_username -p < database/schema.sql

# Load sample data (development only)
mysql -u your_username -p < database/sample_data.sql
```

## Architecture Overview

### MVC Structure
The application follows a custom MVC pattern:

- **Front Controller**: `public/index.php` handles all requests, loads environment, and dispatches to Router
- **Router**: `src/Core/Router.php` maps URLs to controller actions using pattern matching
- **Controllers**: Located in `src/Controllers/`, handle HTTP requests and return responses
- **Models**: Located in `src/Models/`, represent database entities and business logic
- **Views**: Located in `src/Views/`, PHP templates for rendering HTML

### Key Components

1. **Database Layer**: 
   - Uses PDO for database operations via `src/Core/Database.php`
   - All IDs use UUID format
   - Foreign key constraints enforce data integrity

2. **Routing System**:
   - Routes defined in `config/routes.php`
   - Supports parameter extraction (e.g., `/event/{event_id}`)
   - Separate API endpoints for AJAX/real-time updates

3. **Real-time Features**:
   - Pusher integration for WebSocket communication
   - API endpoints in `src/Controllers/API/` for polling/updates

### Database Schema
Key tables:
- `events`: Core event information
- `dates`, `locations`: Voting candidates
- `members`: Event participants
- `items`: Bring-along items with assignments
- `chat_messages`: Event-specific chat
- `votes`: Tracks all voting data

All tables use `event_id` foreign keys for data isolation between events.

### Environment Configuration
The application uses `.env` files for configuration:
- Database connection (MySQL)
- Pusher credentials for real-time features
- Application environment settings

### Error Handling
- Monolog for error logging to `logs/error.log`
- Environment-aware error display (detailed in development, generic in production)
- 404 handling via Router

## Important Notes

- No authentication system - events are accessed via unique UUIDs
- All timestamps use Asia/Tokyo timezone
- File uploads are not currently supported
- Frontend uses Tailwind CSS (CDN-based, no build process)
- No JavaScript build process - vanilla JS for frontend interactions