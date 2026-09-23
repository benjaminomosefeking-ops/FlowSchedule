# FlowSchedule

A shift management system for healthcare professionals that optimizes personnel assignment while complying with labor regulations and ensuring equitable work distribution.

## Features

- Complete management of medical shifts (morning, afternoon, night)
- Intelligent shift assignment based on specific skills (nursing, medicine, radiology)
- Automatic validation of business rules:
  - Minimum 12-hour rest between shifts
  - Maximum 40 weekly hours limit
  - Skill requirement verification for each shift
  - Equitable weekend distribution
- Secure user authentication with Laravel Sanctum
- Personalized dashboard to view assigned shifts
- User profile management
- Onboarding system for new employees
- Calendar view for visual planning
- Task (todo) management and collaborative boards
- API endpoints for shift operations

## Tech Stack

- **Backend**: Laravel 13 (PHP 8.3)
  - Hexagonal architecture/Domain-Driven Design
  - Laravel Sanctum for API authentication
  - Eloquent ORM for data access
  - Form and request validation
- **Frontend**: 
  - Blade templates for server-side rendering
  - Alpine.js for lightweight interactivity
  - Tailwind CSS for utility-first styling
  - Vite as asset bundler
  - FullCalendar for calendar view
  - Drawflow for flow diagrams
  - React for specific components (Excalidraw integration)
- **Database**: MySQL/SQLite
  - Laravel migrations for versioned schema
  - Eloquent models with UUIDs
- **Testing**: PHPUnit
  - Unit tests for domain and services
  - Feature tests for endpoints and user flows

## Architecture

FlowSchedule follows a hexagonal architecture (ports and adapters) organized in the following structure within `src/Bundle/FlowScheduler/`:

### Domain (Domain)
Contains pure business logic without external dependencies:
- **Entities**: Shift, Employee, Assignment - represent core business concepts
- **ValueObjects**: VOBTimeSlot, VOBShiftId, VOBInviteCode - immutable objects with built-in validation
- **Enums**: ShiftType, Skill, AssignmentStatus - defined types to ensure consistency
- **Exceptions**: Domain-specific exceptions for business rules (InsufficientRestException, MaxHoursExceededException, etc.)
- **Services**: SchedulingValidator - implements complex validation rules for assignments
- **Interfaces (Ports)**: Contract definitions for repositories and authenticators

### Application (Application)
Orchestrates system use cases:
- **UseCases**: Application logic like CreateShiftUseCase, RegisterUserUseCase, AuthenticateUserUseCase
- **DTOs**: Data transfer objects for communication between layers (CreateShiftDTO, RegisterUserDTO)

### Infrastructure (Infrastructure)
Technical implementations of domain-defined ports:
- **Persistence**: 
  - Eloquent Models - ORM mapping of entities to tables (ShiftModel, EmployeeModel)
  - Repositories - concrete implementations of repository interfaces (EloquentShiftRepository)
- **Providers**: Service providers for Laravel container
- **Auth**: Specific authentication implementations

### UI (User Interface)
Presentation and interaction layer:
- **Controllers**: HTTP request handling and JSON responses (ShiftController, Auth controllers)
- **Routes**: Web and API endpoint definitions

This separation ensures:
- Framework independence (domain doesn't know Laravel)
- Business logic testability in isolation
- Maintainability through clear responsibility separation
- Scalability to add new functionalities without affecting the core

## Authentication & Security

FlowSchedule implements a robust authentication system based on Laravel Sanctum:

- **Token-based authentication**: Users receive personal access tokens for API requests
- **Route protection**: 'auth' middleware protects all sensitive application routes
- **Session management**: Integration with Laravel's session system for web
- **Password reset**: Standard Laravel functionality for email-based reset
- **Email verification**: Optional to confirm email addresses
- **Authorization approach**: Currently more role-based than fine-grained permissions
- **Role system**: Implemented via migrations adding 'role' fields to users and team user pivots
- **Personal access tokens**: Support for long-duration tokens for external integrations
- **Input validation**: All requests validated at both form and domain levels

The system currently does NOT implement:
- Two-factor authentication (2FA)
- Social login (OAuth)
- Account lockout after failed attempts
- Detailed access auditing

## Database

FlowSchedule uses a relational schema designed to manage shifts, employees, and their assignments:

### Main Entities

**shifts**
- Stores shift definitions
- Fields: id (UUID), title, description, type (morning/afternoon/night), required_skill (nurse/doctor/xray), start_time, end_time, status (pending/confirmed/completed/cancelled), team_id, timestamps

**employees**
- Medical staff information eligible for shift assignment
- Fields: id (UUID), name, email (unique), skills (JSON array), weekend_count (counter for equitable distribution), timestamps

**assignments**
- Links employees to assigned shifts
- Fields: id, employee_id, shift_id, assignment_date, timestamps

**users**
- System access accounts
- Fields: standard Laravel fields plus: role (for permission differentiation), team_id

**teams**
- Groupings of users and employees (useful for clinics/departments)
- Fields: id, name, timestamps

**team_user** (pivot)
- Many-to-many relationship between teams and users

**calendar_events**
- Personal calendar events
- Fields: id, user_id, title, description, start_time, end_time, color, timestamps

**todos**
- Personal or team tasks
- Fields: id, user_id, team_id, title, description, completed, timestamps

**boards**
- Trello-style Kanban boards for visual organization
- Fields: id, user_id, team_id, name, timestamps

**board_todos**
- Relationship between boards and tasks

### Key Relationships
- An employee can have many assigned shifts (through assignments)
- A shift can be assigned to only one employee at a time
- Users belong to teams (and can belong to multiple)
- Employees are associated with teams for assignment filters
- Calendar events belong to specific users

## Testing

FlowSchedule includes an automated test suite to ensure code quality:

### Unit Tests
Isolated tests of individual components:
- **Domain/Entities**: Validation of Shift logic (state transitions, duration calculations)
- **Domain/Services**: Tests of SchedulingValidator for all business rules (rest, max hours, skills, distribution)
- **Domain/ValueObjects**: Verification of VOBTimeSlot and VOBInviteCode (construction validations, access methods)
- **Domain/Exceptions**: Tests that exceptions are thrown under correct conditions

### Feature Tests
Integration tests verifying complete flows:
- **Auth**: Registration, login, password recovery, protected route access
- **Scheduling**: Creation, reading, updating, and deletion of shifts via API
- **Onboarding**: Complete initial registration and profile setup flow
- **Profile**: User information and preference updates
- **Boards and Todos**: Full CRUD operations for boards and tasks
- **Calendar**: Calendar event management (creation, reading, updating, deletion)
- **Authorization**: Verification that users can only access own or team resources
- **Simulation**: Complex multiple shift assignment scenarios

### Test Coverage
Testing particularly covers:
- All shift state transitions (pending → confirmed → completed/cancelled)
- Business rule validation in boundary situations
- Authentication and authorization flows
- Basic operations of all main modules
- Error cases and input validation

To run tests:
```bash
php artisan test
```

For specific tests:
```bash
php artisan test --filter=Scheduling
php artisan test --filter=Auth
```

## Project Structure

```
flowscheduler/
├── app/                     # Standard Laravel code
├── src/                     # Domain core (hexagonal architecture)
│   └── Bundle/
│       └── FlowScheduler/
│           ├── Application/     # Use cases and DTOs
│           ├── Domain/          # Pure business logic
│           │   ├── Entities/    # Shift, Employee, Assignment
│           │   ├── Enums/       # Defined types (ShiftType, Skill, etc.)
│           │   ├── Exceptions/  # Domain exceptions
│           │   ├── Ports/       # Interfaces (repositories, authenticators)
│           │   ├── Services/    # SchedulingValidator
│           │   └── ValueObjects/# VOBTimeSlot, VOBShiftId, etc.
│           ├── Infrastructure/  # Technical implementations
│           │   ├── Auth/        # Authentication providers
│           │   ├── Persistence/ # Eloquent models and repositories
│           │   └── Providers/   # Laravel service providers
│           └── UI/              # Presentation layer
│               ├── Controllers/ # ShiftController, Auth controllers
│               └── Routes/      # API route definitions
├── routes/                  # Laravel web routes (web.php, api.php, auth.php)
├── database/                # Migrations and seeders
│   ├── migrations/          # Versioned database schema
│   ├── factories/           # Factories for testing
│   └── seeders/             # Initial data
├── tests/                   # Automated test suite
│   ├── Feature/             # Integration tests
│   └── Unit/                # Unit tests
├── resources/               # Blade views and assets
│   ├── views/               # Blade templates
│   ├── css/                 # Tailwind styles
│   └── js/                  # Alpine.js JavaScript
├── public/                  # Compiled public assets
├── composer.json            # PHP dependencies
├── package.json             # Node.js dependencies
├── vite.config.js           # Vite configuration
└── tailwind.config.js       # Tailwind CSS configuration
```

## Installation

Follow these steps to install and run FlowSchedule locally:

### Prerequisites

- PHP 8.3 or higher
- Composer (PHP dependency manager)
- Node.js 18+ and npm
- MySQL or SQLite database
- Git

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/benjaminomosefeking-ops/FlowSchedule.git
   cd FlowSchedule
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Configure environment variables**
   ```bash
   cp .env.example .env
   ```
   Edit the `.env` file to configure:
   - Database connection (DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD)
   - Other variables as needed (MAIL_* for email notifications, etc.)

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Create and migrate the database**
   ```bash
   # For SQLite (simplest development option)
   touch database/database.sqlite
   
   # Run migrations
   php artisan migrate
   ```

7. **Compile frontend assets**
   ```bash
   npm run build
   ```
   For development with hot reload:
   ```bash
   npm run dev
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

9. **Access the application**
   Open your browser at `http://127.0.0.1:8000`

### Additional Notes

- The first user can be created via standard registration at `/register`
- To access the dashboard, visit `/dashboard` after logging in
- Installation commands are also available as a composite script: `composer run setup`

## Environment Variables

You must copy `.env.example` to `.env` and configure the following minimally required variables:

### Database Configuration
- `DB_CONNECTION`: Database type (mysql, sqlite, pgsql)
- `DB_HOST`: Database server host (for MySQL/PostgreSQL)
- `DB_PORT`: Connection port (3306 for MySQL, 5432 for PostgreSQL)
- `DB_DATABASE`: Database name
- `DB_USERNAME`: Database username
- `DB_PASSWORD`: Database password

### Application Configuration
- `APP_NAME`: Application name (default: Laravel)
- `APP_ENV`: Execution environment (local, production, testing)
- `APP_KEY`: Encryption key for cookies and data (generated with `php artisan key:generate`)
- `APP_DEBUG`: Show error details (true for development, false for production)
- `APP_URL`: Application base URL (http://localhost:8000 for development)

### Mail Configuration (optional for notifications)
- `MAIL_MAILER`: Mail driver (smtp, sendmail, etc.)
- `MAIL_HOST`: SMTP server
- `MAIL_PORT`: SMTP port
- `MAIL_USERNAME`: SMTP authentication username
- `MAIL_PASSWORD`: SMTP authentication password
- `MAIL_ENCRYPTION`: Encryption type (tls, ssl)
- `MAIL_FROM_ADDRESS`: Sender email address
- `MAIL_FROM_NAME`: Sender name displayed in emails

### Other Configurations
- `SESSION_DRIVER`: Session driver (file, database, redis, etc.)
- `CACHE_DRIVER`: Cache driver (file, database, redis, dynamodb, etc.)
- `QUEUE_CONNECTION`: Queue driver (sync, database, beanstalkd, sqs, redis, etc.)

**Important**: Never commit your `.env` file to public repositories. This file contains sensitive information and must remain private.

## Testing

To run the complete automated test suite:

```bash
php artisan test
```

### Running Specific Tests

- Only feature tests:
  ```bash
  php artisan test --filter=Feature
  ```

- Only unit tests:
  ```bash
  php artisan test --filter=Unit
  ```

- Specific module tests:
  ```bash
  php artisan test --filter=Scheduling
  php artisan test --filter=Auth
  php artisan test --filter=Onboarding
  ```

### Code Coverage

For a detailed code coverage report:
```bash
php artisan test --coverage
```

Tests are designed to run against a separate test database configured in the testing environment (typically SQLite in memory).

## Screenshots

*(Add screenshots here showing:)*
- Dashboard with shift calendar
- New shift creation form
- Shift list filtered by date
- User profile with personal information
- Calendar view with created events
- Kanban board with organized tasks

## Future Improvements

These are planned improvements for future FlowSchedule versions:

### Planned Features
- **Push notifications**: Integration with notification services for shift alerts or last-minute changes
- **Shift trading**: System for employees to propose and approve shift swaps
- **Time-off requests**: Module for vacation, medical leave, or other absence requests
- **Reports and analytics**: Dashboard with workload metrics, shift distribution by skill, personnel costs, etc.
- **External system integration**: APIs to connect with payroll systems, electronic health records (EHR), or clinical management software
- **Mobile application**: Native or PWA app for shift consultation and management from mobile devices
- **Shift preferences**: Allow employees to indicate preferred working hours and days
- **Replacement management**: Automatic system to find substitutes when an employee requests last-minute leave
- **Change history**: Detailed audit of all modifications made to shifts and assignments

### Technical Improvements
- **Migration to Laravel Fortify**: For more robust authentication with email verification and enhanced security
- **Authorization policies**: Implementation of Laravel Gates and Policies for finer access control
- **Query optimization**: Eager loading and specific column selection to improve performance
- **Events and listeners**: Additional decoupling through domain events (ShiftCreated, AssignmentMade, etc.)
- **Strategic caching**: Caching implementation for frequent queries and costly calculations
- **Internationalization (i18n)**: Multi-language support in user interface
- **Acceptance testing**: Implementation of Dusk or Cypress tests for complete user scenarios
- **Continuous integration**: GitHub Actions configuration for automatic testing on each push

### Scalability
- **Database partitioning**: To handle large volumes of historical data
- **Expanded queue usage**: Broader use of Laravel Queues for asynchronously processable operations
- **Frontend optimization**: Code splitting and lazy loading of components to improve initial load times
- **Microservices**: Extraction of certain modules (notifications, reports) as independent services