# Task Management API (Laravel Backend)

This project is the **backend** of the **MileApp Fullstack Test**.  
It is a **mock backend API** built with **Laravel** to demonstrate a simple task management module with full CRUD operations and basic authentication.

---

## 🧩 Overview

The backend exposes the following main endpoints:

| Method   | Endpoint          | Description                                                       |
| -------- | ----------------- | ----------------------------------------------------------------- |
| `POST`   | `/api/login`      | Mock login endpoint returning an access token                     |
| `GET`    | `/api/tasks`      | List tasks with optional filters (`is_completed`, `sortBy`, etc.) |
| `POST`   | `/api/tasks`      | Create a new task                                                 |
| `GET`    | `/api/tasks/{id}` | Show a specific task                                              |
| `PUT`    | `/api/tasks/{id}` | Update an existing task                                           |
| `DELETE` | `/api/tasks/{id}` | Delete a task                                                     |

Authentication is **mocked** for this test.  
You can use any email/password combination that passes validation to simulate a successful login.

---

## 🏗️ Design Decisions

1. **Laravel Framework**  
   I chose Laravel because it’s my main expertise and enables me to build clean, production-ready APIs quickly using its expressive routing, validation, and resource features.

2. **Mocked API (No Real Database)**  
   The test requires a mock API, so CRUD operations use in-memory data for quick testing while keeping realistic API behavior.

3. **OpenAPI / iDoc Integration**  
   API documentation is generated using [Ovac iDoc](https://github.com/ovac/idoc) to produce an OpenAPI-compatible specification.  
   This allows the API to be viewed interactively through ReDoc at:

   **➡️ [API Documentation – /docs](https://mileapp-api.anzar.dev/docs)**

---

## 📦 MongoDB Index Design

This fulfills **Requirement 3: MongoDB Index Script**, providing a sample indexing strategy to illustrate how the API could scale in a real MongoDB environment.

### Index Summary

| Index Name                 | Fields                                     | Purpose                                                                 |
| -------------------------- | ------------------------------------------ | ----------------------------------------------------------------------- |
| `tasks_created_at_desc`    | `created_at`                               | Optimizes default listing of recent tasks                               |
| `tasks_completed_due_date` | `is_completed`, `due_date`                 | Speeds up filtering tasks by completion status and ordering by due date |
| `tasks_completed_at_desc`  | `is_completed`, `completed_at` _(partial)_ | Efficiently retrieves completed tasks sorted by completion date         |

---

## 💪 Strengths

**Simple and Maintainable** - designed for quick demonstration and clear separation of API logic.

- **Framework best practices** - uses Laravel’s request validation, response resources, and naming conventions.
- **Ready for extension** - can be easily connected to a real database (e.g., MongoDB, MySQL) by replacing the mock layer.
- **Documented via OpenAPI** - integrates with iDoc to generate browsable API documentation automatically.

---

## 🚀 How to Run

1. **Clone the repository:**

   ```bash
   git clone https://github.com/zarchp/mileapp-api.git
   cd mileapp-api
   ```

1. Copy `.env.example` file:

   ```bash
   cp .env.example .env
   ```

1. Install dependencies:

   ```bash
   composer install
   ```

1. Generate the application key:

   ```bash
   php artisan key:generate
   ```

1. Run migration:

   ```bash
   php artisan migrate
   ```

1. Run the test suite (Pest):

   ```bash
   ./vendor/bin/pest
   ```

1. Start the local development server:

   ```bash
   php artisan serve
   ```

1. View the API documentation at:

   ```bash
   http://localhost:8000/docs
   ```

---

**Project:** MileApp Fullstack Test  
**Author:** Muhamad Anzar Syahid  
**Tech Stack:** Laravel 12 · PHP 8.4 · iDoc · MongoDB (index reference only)
