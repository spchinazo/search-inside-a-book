# Search Inside a Book (Laravel, Laravel Scout & Meilisearch)

This project implements a backend solution for full-text search within digitized books, using Laravel, Laravel Scout, and Meilisearch for powerful, fast filtering and indexing.

The solution focuses on clean API design, robust containerization via Docker, and adherence to modern PHP development standards.

## Getting Started

These instructions will get your project environment up and running on your local machine.

### Prerequisites

* **Docker Desktop:** Required to run the application via Laravel Sail.
* **PHP:** Included in the Sail container.
* **Composer:** Included in the Sail container.

### Installation and Setup

1.  **Clone the Repository:**
    ```bash
    git clone [YOUR_REPO_URL] search-inside-a-book
    cd search-inside-a-book
    ```

2.  **Install Dependencies & Build Containers:**
    Use Laravel Sail to start the environment and install PHP dependencies.
    ```bash
    # Install PHP dependencies and ensure environment is ready
    composer install

    # Start the Docker containers (application, PostgreSQL, Meilisearch)
    ./vendor/bin/sail up -d
    ```

3.  **Configure Environment Variables:**
    Ensure your `.env` file is configured correctly for the database and Meilisearch.

    | Variable | Value | Status |
    | :--- | :--- | :--- |
    | `APP_PORT` | `8888` | Configured to avoid port conflicts. |
    | `DB_HOST` | `pgsql` | Correct service name. |
    | `MEILISEARCH_HOST` | `http://meilisearch:7700` | Correct service name. |
    | `MEILISEARCH_MASTER_KEY` | `[YOUR_MASTER_KEY]` | **Must match** the key used in `docker-compose.yml`. |

4.  **Database Migration and Seeding:**
    Run migrations to create the tables and seed data (which includes book content).

    ```bash
    # Run database migrations
    ./vendor/bin/sail artisan migrate

    # Seed the database with sample data (e.g., Book and BookPages)
    ./vendor/bin/sail artisan db:seed
    ```

## Meilisearch Indexing and Configuration

After the database is seeded, the book content must be indexed for search functionality.

1.  **Synchronize Index Settings:**
    The `book_id` attribute must be configured as filterable in Meilisearch to allow scoped searches (e.g., searching only within Book ID 17).

    ```bash
    # Synchronizes filterable attributes (book_id) to Meilisearch
    ./vendor/bin/sail artisan scout:sync-index-settings
    ```

2.  **Import Data to Meilisearch:**
    Index all records from the `book_pages` table into Meilisearch.

    ```bash
    # Imports all App\Models\BookPage records into the Meilisearch index
    ./vendor/bin/sail artisan scout:import "App\Models\BookPage"
    ```

## API Endpoints

The core functionality is exposed via the following REST API endpoints:

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/api/books/{book}/search?q={query}` | Searches for a term (`q`) within the pages of a specific book. Returns snippets. |
| `GET` | `/api/pages/{page}` | Retrieves the full content of a specific `BookPage` by its ID. |

### Example Request (Search)

To search for the term "DOM" in the book with ID 17:
GET http://localhost:8888/api/books/17/search?q=DOM


**Example Successful Response (200 OK):**

```json
{
    "data": [
        {
            "id": 483,
            "page_number": 436,
            "snippet": "dom.appendChild(child); } return dom; } A display is created by giving it a parent element to which it should append itself and a level object. class ...",
            "full_page_url": "http://localhost:8888/api/pages/483"
        },
        {
            "id": 211,
            "page_number": 164,
            "snippet": "Dominantwritingdirection Write a function that computes the dominant writing direction in a string of text. Remember that each script object has a dir...",
            "full_page_url": "http://localhost:8888/api/pages/211"
        },
        {
            "id": 585,
            "page_number": 538,
            "snippet": "this.dom = elt(\"canvas\", { onmousedown: event => this.mouse(event, pointerDown), ontouchstart: event => this.touch(event, pointerDown) }); this.syncSt...",
            "full_page_url": "http://localhost:8888/api/pages/585"
        }
        ...
    ]
}

## Evidence of Backend Functionality

This section serves as proof of the backend components working correctly, as requested by the project scope.

---

### 1. Database Seeding Proof

This demonstrates that the application successfully migrated the schema and loaded data into the `books` and `book_pages` tables.

**Command Used:** `./vendor/bin/sail artisan db:seed`

![Screenshot of successful db:seed terminal output] (seed.png)

---

### 2. Data Persistence Proof (DBeaver/SQL Client)

This validates that the data is correctly persisted in the PostgreSQL database, confirming the presence of the seeded book pages.

**Query Used:** `SELECT * FROM book_pages WHERE book_id = 17 LIMIT 5;`

![Screenshot of DBeaver/SQL client showing query results] (querysql.png)

---

### 3. API Search Functionality Proof (Postman)

This verifies that the entire pipeline—Laravel, Scout configuration, Meilisearch indexing, Controller logic, and Resource formatting—is functional.

**Request:** `GET http://localhost:8888/api/books/17/search?q=DOM`

![Screenshot of Postman request and JSON 200 OK response] (get-postman.png)