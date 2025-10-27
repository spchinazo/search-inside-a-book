# Search Inside a Book (Laravel, Laravel Scout & Meilisearch)

This project implements a backend solution for full-text search within digitized books, using Laravel, Laravel Scout, and Meilisearch for powerful, fast filtering and indexing.
The solution focuses on clean API design, robust containerization via Docker, and adherence to modern PHP development standards.

### Track Choice: Fullstack Backend Mindset

I chose the **Fullstack Backend Mindset** track because the core challenge—implementing robust, fast, and relevant search inside large documents—is fundamentally a backend and data indexing problem.

This approach allowed me to prioritize:

1.  **API Contract Robustness:** Defining clear, version-agnostic REST endpoints (`/search`, `/pages/{page}`) and ensuring the `SearchResultResource` handles data transformation cleanly, especially for search highlights.
2.  **Indexing Performance and Relevance:** Deep-diving into **Meilisearch** configuration, understanding why `book_id` must be filterable, and implementing an advanced search strategy by passing a custom closure to the `BookPage::search()` method. This **directly manipulates the Meilisearch client** to inject native options and retrieve the raw result set (`->raw()`).
3.  **Validation and Reliability:** Using Docker/Sail for containerization and focusing on **Feature Tests** to validate the entire backend pipeline, ensuring the core functionality (including search highlighting) is reliable and observable.

By focusing on the backend, I built a foundation that any future frontend (Livewire, React, or Mobile) can consume reliably and efficiently.

### Major Technical Challenges

1.  **Meilisearch Asynchronous Configuration (`waitForTask`):**
    * **The Problem:** Meilisearch tasks (like updating `filterableAttributes` for `book_id`) are asynchronous. In a test environment, if the next operation (the search query) runs before the configuration task is complete, the search fails with an error (`book_id is not filterable`).
    * **The Fix:** We had to manually implement the Meilisearch PHP client's `->waitForTask($taskUid, 5000)` function in the `setUp()` method of the feature tests. This forced the test suite to pause until Meilisearch confirmed the configuration changes were active, ensuring reliable test execution.

2.  **Data Persistence in Docker/Sail:**
    * **The Problem:** During development, the database data and Meilisearch index data were frequently lost, forcing repetitive re-runs of `db:seed` and `scout:import`.
    * **The Cause:** This was traced to the incorrect use of the destructive Docker command `sail down -v`, which removes persistent volumes.
    * **The Fix:** The documentation now explicitly advises against `sail down -v` and uses the robust `sail up --build -d` sequence to maintain the integrity of the `sail-pgsql` and `sail-meilisearch` volumes.

## Getting Started

These instructions will quickly set up the entire development environment on your local machine.

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
    # 1. Create tables
    ./vendor/bin/sail artisan migrate

    # 2. Seed the database with sample book content (e.g., Eloquent JavaScript)
    ./vendor/bin/sail artisan db:seed

    # 3. Import data to Meilisearch and synchronize settings
    # NOTE: This ensures 'book_id' is filterable and all pages are indexed.
    ./vendor/bin/sail artisan scout:import "App\Models\BookPage"
    ./vendor/bin/sail artisan scout:sync-index-settings
    ./vendor/bin/sail artisan scout:sync
    ```

## Meilisearch Indexing and Configuration

After the database is seeded, the book content must be indexed for search functionality. **These steps are already included in the `Installation and Setup` section.**

The two key operations are:

1.  **Synchronize Index Settings:** This step ensures the `book_id` attribute is configured as filterable in Meilisearch, which is crucial for allowing scoped searches (e.g., searching only within a specific Book ID).
2.  **Import Data to Meilisearch:** This imports all records from the `book_pages` table into the search engine's index.

## API Endpoints

The core functionality is exposed via the following REST API endpoints:

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/api/books/{book}/search?q={query}` | **Scoped Search:** Searches for a term (`q`) within the pages of a specific `{book}` ID. Returns highlighted snippets and metadata. |
| `GET` | `/api/pages/{page}` | **Full Content Retrieval:** Retrieves the complete text content of a specific `BookPage` by its ID. |
| `GET` | `/api/books` | **List All Books:** Retrieves the list of available books in the system. |

### Example Request (Search)

To search for the term "DOM" in the book with ID 17:
GET http://localhost:8888/api/books/17/search?q=DOM


**Example Successful Response (200 OK):**

```json
{
    [
        {
            "id": 237,
            "page_number": 211,
            "snippet": "solve each of these <em>tasks</em>. When done, it should output the average number of steps each robot took per <em>task</em>. For the sake of fairnes ...",
            "full_page_url": "http://localhost:8888/api/pages/237"
        },
        {
            "id": 152,
            "page_number": 126,
            "snippet": "... tion getTask() { return todoList.shift(); } function rememberUrgently(<em>task</em>) { todoList.unshift(<em>task</em>); } That program manages a queue ...",
            "full_page_url": "http://localhost:8888/api/pages/152"
        },...
    ]
}
```

## Evidence of Backend Functionality

This section serves as proof of the backend components working correctly, as requested by the project scope.

---

### 1. Database Seeding Proof

This demonstrates that the application successfully migrated the schema and loaded data into the `books` and `book_pages` tables.

**Command Used:** `./vendor/bin/sail artisan db:seed`

![Screenshot of successful db:seed terminal output] (seed.png)

**IMPORTANT**  
Always run this command to sync the registers with the database:

```bash
./vendor/bin/sail artisan scout:sync
```
![Screenshot of successful db:seed terminal output] (seedandsync.png)

---

### 2. Data Persistence Proof (DBeaver/SQL Client)

This validates that the data is correctly persisted in the PostgreSQL database, confirming the presence of the seeded book pages.

**Query Used:** `SELECT * FROM book_pages WHERE book_id = 17 LIMIT 5;`

![Screenshot of DBeaver/SQL client showing query results] (querysql.png)

---

### 3. API Search Functionality Proof (Postman)

This verifies that the entire pipeline—Laravel, Scout configuration, Meilisearch indexing, Controller logic, and Resource formatting—is functional.

**Request:** `GET http://localhost:8888/api/books/{book}/search?q=DOM`

![Screenshot of Postman request and JSON 200 OK response] (highlight.png)

**Request:** `GET http://localhost:8888/api/books`

![Screenshot of Postman request and JSON 200 OK response] (searchallbooks.png)

**Request:** `GET http://localhost:8888/api/pages/{page}`

![Screenshot of Postman request and JSON 200 OK response] (searchallbooks.png)

**Request:** `GET http://localhost:8888/api/books/{book}/search?q=`

![Screenshot of Postman request and JSON 400 OK response] (invalidparam.png)

---

### 4. Tests
All core functionality, including the essential **search highlighting** and API structure, is validated via feature tests.

```bash
# Run all feature tests to validate the backend pipeline
./vendor/bin/sail artisan test
```
![Screenshot of all tests passing] (alltests.png)

## Fullstack Backend Mindset

This solution was developed with a **Fullstack Backend Mindset**, prioritizing a **robust API contract**, **optimal data indexing**, and **high performance**.

**Why Meilisearch?**

Meilisearch was chosen for its excellent **developer experience** and **near-instant search performance**. Its built-in **typo tolerance** and seamless integration with **Laravel Scout** drastically reduce the time and complexity required to build a highly relevant search feature compared to heavier alternatives like Elasticsearch.

### API Architecture

* **Scoped Search:** The `book_id` is configured as a **filterable attribute** in Meilisearch, allowing the search to be scoped correctly. The filtering logic is passed directly to the Meilisearch API query via the `filter` option.
* **Highlighting Logic (Native Access):** Instead of relying on the `->withPending()` macro (which was incompatible with the current Laravel Scout version), the `BookSearchController` uses the **native Meilisearch client** via the `search($query, function...)` syntax. This allows explicit injection of the `attributesToHighlight`, `highlightPreTag`, and `highlightPostTag` options to guarantee the returned snippet contains the `<em>` tags.
* **Data Transformation:** The raw results (JSON `hits`) from Meilisearch are mapped to generic PHP objects, ensuring the essential `_formatted` metadata—containing the highlighted snippet—is preserved. The `SearchResultResource` then extracts this highlighted content and presents it as the final `snippet` field in the API response.

### Trade-offs and Assumptions

| Area | Trade-off Made | Assumption |
| :--- | :--- | :--- |
| **API vs UI** | No UI was implemented. | The consuming client (web/mobile) will handle the presentation and interaction logic (e.g., Livewire or React). |
| **Authentication** | API endpoints are publicly accessible. | Security (e.g., using Laravel Sanctum) would be the immediate next step in a production environment. |
| **Snippets** | Snippets use Meilisearch's default cropping. | The frontend is assumed to handle the rendering of `<em>` tags safely for the highlighting effect. |

## Think Big: Roadmap for 2-3 Months

Assuming a 2 to 3-month horizon and the consolidation of the **Fullstack Backend Mindset**, the main focus will be the transition from a functional proof-of-concept to a **production-grade solution** in terms of **Relevance, Performance**, and **Scalability**.

### Month 1: Focus on Relevance and Asynchronous Performance

The initial goal is to improve the quality of search results (relevance) and ensure that write operations do not impact application performance.

| Area | Task | Technical Detail |
| :--- | :--- | :--- |
| **Relevance** | **Optimize Ranking Rules** | Apply **Custom Ranking Rules** in Meilisearch. For example, promoting documents where the search term appears in the `page_number` or the book title (if we were indexing the title as well). |
| **Relevance** | **Synonyms and Stop Words** | Configure lists of synonyms (`e.g., "js" -> "javascript"`) and remove irrelevant *stop words* (`e.g., "the", "a", "an"`) to increase the accuracy of natural language search. |
| **Performance** | **Asynchronous Indexing (Queues)** | Implement **Laravel Queues (Redis)** for all write operations (creation, update, deletion of pages/books). Setting `scout.queue: true` ensures that `$model->save()` does not make synchronous API calls to Meilisearch, allowing the web request to return quickly. |
| **Maintenance** | **Laravel Scout Update** | Attempt to update the `laravel/scout` dependency to a version that officially supports the `->withPending()` method, allowing the removal of the native access solution (`searchRaw`) and simplifying the Controller. |

### Month 2: Focus on Hardening and Observability

The second month would focus on solidifying the solution for a production environment, adding security and monitoring tools.

| Area | Task | Technical Detail |
| :--- | :--- | :--- |
| **Security** | **API Authentication (Laravel Sanctum)** | Implement authentication on the `/api/*` routes. The consuming client (frontend/mobile) would pass an access token, ensuring only authorized users can search and view content. |
| **Observability** | **Logging and Error Monitoring** | Configure Laravel to send critical errors (such as Meilisearch connection failures or Indexing errors) to a service like Sentry or Logtail, ensuring infrastructure issues are detected proactively. |
| **Security/Scope** | **Tenant Tokens (If needed)** | If the project evolves into a *multi-tenant* model (multiple users/clients using the same instance), generating **Meilisearch Tenant Tokens** would be implemented to enforce security filters **on the Meilisearch side**, ensuring the `book_id` or `user_id` cannot be bypassed by the frontend. |
| **Performance** | **Query Optimization (Caching)** | Implement caching for high-frequency search results (using Redis) or for pages that rarely change (`/api/pages/{page}`), reducing the load on Meilisearch. |

### Month 3: Focus on Scalability and Future Growth

The final month would focus on preparing the system for massive data growth and additional functionality.

| Area | Task | Technical Detail |
| :--- | :--- | :--- |
| **Scalability** | **Hardware/Infra Optimization** | Evaluate the need for scaling Meilisearch horizontally (clustering or index *sharding*) or scaling the database server (PostgreSQL) and queue workers. |
| **Functionality** | **Federated Search** | Expand the search to include multiple indexes (e.g., a `Books` index and an `Authors` index), allowing the unified search to return results from different entities. |
| **Maintenance** | **API Versioning** | Adopt API versioning (`/api/v1/books/...`), making it easier to introduce future architectural changes without breaking existing clients (e.g., v1 keeps the current snippet format, v2 introduces a richer *hit* format). |

## Presentation Outline

This is a brief outline of the topics I will cover during the final presentation:

1.  **Hands-on Solution:** A live demo of the API endpoints (Search and Full Page Retrieval).
2.  **Technical Deep Dive:**
    * Rationale for Meilisearch (Why vs. alternatives).
    * Challenge Overcome: Bypassing the `withPending()` error using **Native Meilisearch Client Access**.
    * API Architecture and Data Transformation (`SearchResultResource` logic).
3.  **Trade-offs:** The key decisions made (e.g., no UI, no Auth).