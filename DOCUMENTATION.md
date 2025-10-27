# Search Inside a Book (Laravel, Laravel Scout & Meilisearch)

This project implements a backend solution for full-text search within digitized books, using Laravel, Laravel Scout, and Meilisearch for powerful, fast filtering and indexing.
The solution focuses on clean API design, robust containerization via Docker, and adherence to modern PHP development standards.

## Track Choice: Fullstack Backend Mindset

I chose the **Fullstack Backend Mindset** track because the core challenge—implementing robust, fast, and relevant search inside large documents—is fundamentally a backend and data indexing problem.

**Why this track?**
The exercise requires solving complex problems around search relevance, performance, and scalability. These are backend-first challenges where the quality of the API directly determines the frontend experience. By focusing on the backend, I could:
- Build a solid foundation that any frontend can consume
- Ensure search is fast, accurate, and maintainable
- Validate the entire pipeline with automated tests

## AI Tools Used

During the development of this project, I leveraged AI assistance to accelerate certain tasks:

- **GitHub Copilot**: For code completion and boilerplate generation (Controllers, Resources, Tests)
- **Claude AI**: For architectural decisions, documentation writing, and problem-solving discussions
- **ChatGPT**: For debugging complex Meilisearch configuration issues

These tools helped me focus on the core challenge (search relevance and performance) rather than spending time on repetitive tasks. However, all architectural decisions, trade-offs, and implementation strategies were carefully evaluated and are my own.

This approach allowed me to prioritize:

- **API Contract Robustness**: Defining clear, version-agnostic REST endpoints (`/search`, `/pages/{page}`) with proper validation using Form Requests, ensuring the `SearchResultResource` handles data transformation cleanly, especially for search highlights.

- **Indexing Performance and Relevance**: Deep-diving into Meilisearch configuration, understanding why `book_id` must be filterable, and implementing an advanced search strategy by passing a custom closure to the `BookPage::search()` method. This directly manipulates the Meilisearch client to inject native options and retrieve the raw result set (`->raw()`).

- **Validation and Reliability**: Using Docker/Sail for containerization and focusing on Feature Tests to validate the entire backend pipeline, ensuring the core functionality (including search highlighting and pagination) is reliable and observable.

By focusing on the backend, I built a foundation that any future frontend (Livewire, React, or Mobile) can consume reliably and efficiently.

## Getting Started

These instructions will quickly set up the entire development environment on your local machine.

### Prerequisites

- **Docker Desktop**: Required to run the application via Laravel Sail.
- **PHP**: Included in the Sail container.
- **Composer**: Included in the Sail container.

### Installation and Setup

1. **Clone the Repository**:
   ```bash
   git clone [YOUR_REPO_URL] search-inside-a-book
   cd search-inside-a-book
   ```

2. **Install Dependencies & Build Containers**:
   Use Laravel Sail to start the environment and install PHP dependencies.
   ```bash
   # Install PHP dependencies and ensure environment is ready
   composer install

   # Start the Docker containers (application, PostgreSQL, Meilisearch)
   ./vendor/bin/sail up -d
   ```

3. **Configure Environment Variables**:
   Ensure your `.env` file is configured correctly for the database and Meilisearch. 
   
   **Note on Queue Configuration:**
   - Use `QUEUE_CONNECTION=redis` if you want to enable Laravel Horizon (recommended for production)
   - Use `QUEUE_CONNECTION=async` for simpler development setup without Horizon
   - Both options support asynchronous indexing with Scout

   | Variable | Value | Status |
   | :--- | :--- | :--- |
   | APP_PORT | 8888 | Configured to avoid port conflicts. |
   | DB_HOST | pgsql | Correct service name. |
   | MEILISEARCH_HOST | http://meilisearch:7700 | Correct service name. |
   | MEILISEARCH_MASTER_KEY | [YOUR_MASTER_KEY] | Must match the key used in docker-compose.yml. |
   | QUEUE_CONNECTION | redis | Required for Laravel Horizon. Use `async` for simpler setup without Horizon. |
   | CACHE_STORE | redis | Required for optimal cache performance. |

4. **Database Migration and Seeding**:
   Run migrations to create the tables and seed data (which includes book content).

   ```bash
   # 1. Create tables
   ./vendor/bin/sail artisan migrate

   # 2. Seed the database with sample book content (e.g., Eloquent JavaScript)
   ./vendor/bin/sail artisan db:seed

   # 3. Configure Meilisearch index settings (ranking, filters, etc.)
   ./vendor/bin/sail artisan meilisearch:configure

   # 4. Import data to Meilisearch
   ./vendor/bin/sail artisan scout:import "App\Models\BookPage"
   ```

**NOTE on Docker Persistence**:
During development, data was lost due to improper shutdown. To maintain the integrity of the PostgreSQL and Meilisearch data volumes, avoid using the destructive command `sail down -v`. Use `sail down` to stop containers safely.

## Performance, Asynchronicity, and Scalability

To ensure API performance is maintained, even with large volumes of data or during the import of new books, Laravel Queues (Redis) and Laravel Horizon have been implemented.

### 1. Queue Strategy (Queues) with Redis and Horizon

Data indexing in Meilisearch is an I/O (input/output) operation that consumes time. Executing it synchronously (in the same HTTP request) would degrade the user experience.

- **Redis**: Configured as the broker (manager) for Laravel's queues (`QUEUE_CONNECTION=redis`). Redis is an in-memory store that holds indexing "jobs" extremely fast.

- **Laravel Horizon**: Is the management dashboard and worker supervisor for Redis. It ensures that workers (processes that execute the Jobs) are always active, monitoring the queues.

- **Asynchronicity (ShouldQueue)**: The `App\Models\BookPage` model implements the `ShouldQueue` trait. This ensures that CRUD operations (Create, Update, Delete) on the model do not make synchronous API calls to Meilisearch; instead, they send a Job to Redis.

**Essential Commands**:

To process indexing in the background, Horizon must always be active:

```bash
# Horizon installation (once)
composer require laravel/horizon
./vendor/bin/sail artisan horizon:install

# Starts the queue processing (must be kept active in a separate terminal)
./vendor/bin/sail artisan horizon
```

### 2. Cache Implementation (Redis)

Redis is also used as the application's primary cache store, being the fastest and most suitable driver for production use.

**Motivation**:

- **Reducing PostgreSQL Latency**: The `BookSearchController@index` method (listing all books) performs a simple query, but in a high-traffic environment, this query can overload the database.

- **Caching**: The list of books (`/api/books`) is stored in Redis for an extended period (and invalidated only when a book is added/removed), drastically reducing queries to PostgreSQL.

- **Page Content Caching**: Full page content (`/api/pages/{page}`) is cached for 60 minutes since book content rarely changes.

**Where Cache is Used**:

```php
// Listing all books
$books = Cache::remember('all_books_list', 3600, function () {
    return Book::all();
});

// Full page content
$pageData = Cache::remember("full_page_content_{$page->id}", 3600, function () use ($page) {
    return [
        'page_number' => $page->page_number,
        'content' => $page->text_content,
        'book_title' => $page->book->title,
    ];
});
```

## API Endpoints

The core functionality is exposed via the following REST API endpoints:

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| GET | `/api/books/{book}/search?q={query}&page={page}` | **Scoped Search**: Searches for a term (q) within the pages of a specific {book} ID. Returns paginated, highlighted snippets with relevance scores. |
| GET | `/api/pages/{page}` | **Full Content Retrieval**: Retrieves the complete text content of a specific BookPage by its ID. |
| GET | `/api/books` | **List All Books**: Retrieves the list of available books in the system. |
| GET | `/api/health` | **Health Check**: Validates that all services (database, cache, Meilisearch) are operational. |

## Advanced Features Implemented

### Pagination
Search results are paginated with 20 results per page. Use the `page` parameter to navigate through results:

```
GET /api/books/1/search?q=DOM&page=2
```

Response includes comprehensive pagination metadata:

```json
{
    "data": [...],
    "meta": {
        "total": 45,
        "per_page": 20,
        "current_page": 2,
        "last_page": 3
    }
}
```

### Relevance Scoring
Each search result includes a `relevance_score` (0-1) calculated by Meilisearch's ranking algorithm, allowing frontends to display confidence indicators or implement custom sorting.

### Observability
- `/api/health` endpoint monitors database, cache, and Meilisearch availability
- Search queries are logged to `storage/logs/searches.log` for analysis of search patterns, most frequent queries, and potential abuse detection
- Logs include query, book_id, IP address, and timestamp for comprehensive analytics

### Input Validation
Robust input validation using Laravel Form Requests with detailed error messages:
- Query must be 2-200 characters
- Page parameter must be a positive integer
- Returns 422 status with clear validation errors

### Rate Limiting Strategy

The API implements differentiated rate limiting based on operation cost:

- **Search operations** (`/books/{book}/search`): 60 requests/minute
  - Queries Meilisearch directly (I/O intensive)
  - More restrictive to protect search infrastructure
  
- **Read operations** (`/books`, `/pages/{page}`): 120 requests/minute
  - Served from Redis cache (60min TTL)
  - More permissive since cache layer absorbs load
  - Cache invalidation only on book creation/deletion

### Meilisearch Configuration

A dedicated Artisan command optimizes the search index:

```bash
./vendor/bin/sail artisan meilisearch:configure
```

This command configures:
- **Ranking Rules**: Optimized for relevance (words, typo, proximity, attribute, sort, exactness)
- **Searchable Attributes**: text_content with appropriate weights
- **Filterable Attributes**: book_id for scoped searches
- **Sortable Attributes**: page_number for future sorting features
- **Typo Tolerance**: Configured for optimal user experience (4 chars for one typo, 8 for two)

### Example Request (Search)

To search for the term "DOM" in the book with ID 1:
```
GET http://localhost:8888/api/books/1/search?q=DOM
```

**Example Successful Response (200 OK)**:

```json
{
    "data": [
        {
            "id": 237,
            "page_number": 211,
            "snippet": "solve each of these <em>tasks</em>. When done, it should output the average number of steps each robot took per <em>task</em>. For the sake of fairnes...",
            "full_page_url": "http://localhost:8888/api/pages/237",
            "relevance_score": 0.9234
        },
        {
            "id": 152,
            "page_number": 126,
            "snippet": "...tion getTask() { return todoList.shift(); } function rememberUrgently(<em>task</em>) { todoList.unshift(<em>task</em>); } That program manages a queue...",
            "full_page_url": "http://localhost:8888/api/pages/152",
            "relevance_score": 0.8756
        }
    ],
    "meta": {
        "total": 15,
        "per_page": 20,
        "current_page": 1,
        "last_page": 1
    }
}
```

### Example Request (Health Check)

```
GET http://localhost:8888/api/health
```

**Example Response (200 OK)**:

```json
{
    "status": "healthy",
    "timestamp": "2025-10-27T14:30:00Z",
    "services": {
        "database": "healthy",
        "cache": "healthy",
        "meilisearch": "healthy"
    }
}
```

## Evidence of Backend Functionality

This section serves as proof of the backend components working correctly, as requested by the project scope.

### 1. Database Seeding Proof

This demonstrates that the application successfully migrated the schema and loaded data into the `books` and `book_pages` tables.

**Command Used**: `./vendor/bin/sail artisan db:seed`

![Screenshot of successful db:seed terminal output](seed.png)

![Screenshot of successful db:seed terminal output](seedandsync.png)

### 2. Data Persistence Proof (DBeaver/SQL Client)

This validates that the data is correctly persisted in the PostgreSQL database, confirming the presence of the seeded book pages.

**Query Used**: `SELECT * FROM book_pages WHERE book_id = 17 LIMIT 5;`

![Screenshot of DBeaver/SQL client showing query results](querysql.png)

### 3. API Search Functionality Proof (Postman)

This verifies that the entire pipeline—Laravel, Scout configuration, Meilisearch indexing, Controller logic, and Resource formatting—is functional.

**Request**: `GET http://localhost:8888/api/books/{book}/search?q=DOM`

![Screenshot of Postman request and JSON 200 OK response with highlighting](highlight.png)
![Screenshot of Postman request showing pagination metadata](pagination.png)

**Request**: `GET http://localhost:8888/api/books`

![Screenshot of Postman request and JSON 200 OK response](searchallbooks.png)

**Request**: `GET http://localhost:8888/api/pages/{page}`

![Screenshot of Postman request and JSON 200 OK response](searchallbooks.png)

**Request**: `GET http://localhost:8888/api/books/{book}/search?q=` (Invalid - missing query)

![Screenshot of Postman request and JSON 422 validation error response](invalidparam.png)

**Request**: `GET http://localhost:8888/api/health`

![Screenshot of Postman request showing all services healthy](health.png)

### 4. Tests

All core functionality, including the essential search highlighting, pagination, validation, and API structure, is validated via comprehensive feature tests.

```bash
# Run all feature tests to validate the backend pipeline
./vendor/bin/sail artisan test
```

![Screenshot of all tests passing](alltests.png)

**Test Coverage Includes**:
- Search with highlighting and pagination
- Query parameter validation (required, min/max length)
- Scoped search (results filtered by book_id)
- Health endpoint checks for all services
- Cache layer functionality
- Rate limiting enforcement

**Technical Note: Asynchronous Configuration in Tests**

Meilisearch configuration tasks (like setting `filterableAttributes` for `book_id`) are asynchronous. To prevent test failures, the `setUp()` method manually implements the Meilisearch PHP client's `->waitForTask($taskUid, 5000)` functionality. This ensures the test execution pauses until Meilisearch confirms the configuration is active.

## Fullstack Backend Mindset

This solution was developed with a **Fullstack Backend Mindset**, prioritizing a robust API contract, optimal data indexing, and high performance.

### Why Meilisearch?

Meilisearch was chosen for its excellent developer experience and near-instant search performance. Its built-in typo tolerance and seamless integration with Laravel Scout drastically reduce the time and complexity required to build a highly relevant search feature compared to heavier alternatives like Elasticsearch.

### API Architecture

- **Scoped Search**: The `book_id` is configured as a filterable attribute in Meilisearch, allowing the search to be scoped correctly. The filtering logic is passed directly to the Meilisearch API query via the `filter` option.

- **Highlighting Logic (Native Access)**: Instead of relying on the `->withPending()` macro (which was incompatible with the current Laravel Scout version), the `BookSearchController` uses the native Meilisearch client via the `search($query, function...)` syntax. This allows explicit injection of the `attributesToHighlight`, `highlightPreTag`, `highlightPostTag`, and `cropLength` options to guarantee the returned snippet contains the `<em>` tags and consistent snippet lengths.

- **Data Transformation**: The raw results (JSON hits) from Meilisearch are passed as arrays to the `SearchResultResource`, ensuring the essential `_formatted` metadata—containing the highlighted snippet—is preserved. The Resource then extracts this highlighted content and presents it as the final `snippet` field in the API response, along with relevance scores and pagination metadata.

- **Form Request Validation**: The `SearchBookRequest` handles all input validation, separating concerns and providing clear, testable validation rules with custom error messages.

## Trade-offs and Assumptions

| Area | Trade-off Made | Assumption |
| :--- | :--- | :--- |
| API vs UI | No UI was implemented. | The consuming client (web/mobile) will handle the presentation and interaction logic (e.g., Livewire or React). |
| Authentication | API endpoints are publicly accessible. | Security (e.g., using Laravel Sanctum) would be the immediate next step in a production environment. |
| Snippets | Snippets use Meilisearch's cropping (200 chars). | The frontend is assumed to handle the rendering of `<em>` tags safely for the highlighting effect. |
| Search Logging | Query logs are stored locally. | In production, these would be sent to a centralized logging service (Logtail, Papertrail) for analysis and monitoring. |
| Pagination | Fixed at 20 results per page. | This default provides good balance between data transfer and user experience. Could be made configurable via query parameter in future iterations. |

## Think Big: Roadmap for 2-3 Months

Assuming a 2 to 3-month horizon and the consolidation of the Fullstack Backend Mindset, the main focus will be the transition from a functional proof-of-concept to a production-grade solution in terms of **Relevance**, **Performance**, and **Scalability**.

### Month 1: Focus on Relevance and Advanced Search Features

The initial goal is to improve the quality of search results and expand search capabilities.

| Area | Task | Technical Detail |
| :--- | :--- | :--- |
| Relevance | Optimize Ranking Rules | Apply Custom Ranking Rules in Meilisearch. Promote documents where the search term appears in specific positions or contexts. Experiment with custom ranking attributes based on page importance or book structure. |
| Relevance | Synonyms and Stop Words | Configure lists of synonyms (e.g., "js" -> "javascript", "dom" -> "document object model") and remove irrelevant stop words to increase the accuracy of natural language search. Analyze query logs to identify common synonym patterns. |
| Functionality | Faceted Search | Implement faceted filtering allowing users to filter results by book chapters, sections, or custom metadata. Add `/api/books/{book}/facets` endpoint to expose available filters. |
| Analytics | Search Analytics Dashboard | Build analytics based on search logs: most common queries, zero-result searches, average relevance scores, query performance metrics. Use this data to continuously improve ranking and relevance. |
| Relevance | Query Suggestions | Implement query auto-completion and "did you mean" suggestions using Meilisearch's typo tolerance and prefix search capabilities. |

### Month 2: Focus on Hardening and Observability

The second month would focus on solidifying the solution for a production environment, adding security and monitoring tools.

| Area | Task | Technical Detail |
| :--- | :--- | :--- |
| Security | API Authentication (Laravel Sanctum) | Implement authentication on the `/api/*` routes. The consuming client (frontend/mobile) would pass an access token, ensuring only authorized users can search and view content. Implement role-based access for different book collections. |
| Observability | Distributed Tracing | Integrate with OpenTelemetry or similar for distributed tracing across the entire search pipeline (API → Scout → Meilisearch → Database). Track request latency, error rates, and bottlenecks. |
| Observability | Error Monitoring | Configure Laravel to send critical errors (such as Meilisearch connection failures or Indexing errors) to a service like Sentry, ensuring infrastructure issues are detected proactively. Set up alerts for degraded service health. |
| Security | Tenant Tokens | If the project evolves into a multi-tenant model, generate Meilisearch Tenant Tokens to enforce security filters on the Meilisearch side, ensuring the `book_id` or `user_id` cannot be bypassed by the frontend. |
| Performance | Advanced Caching Strategy | Implement tiered caching: L1 (in-memory), L2 (Redis), with intelligent cache warming for popular searches. Implement cache tags for granular invalidation when book content updates. |
| Testing | Load Testing | Conduct load testing with realistic traffic patterns to identify bottlenecks. Use tools like Apache JMeter or k6 to simulate concurrent users and validate rate limiting effectiveness. |

### Month 3: Focus on Scalability and Future Growth

The final month would focus on preparing the system for massive data growth and additional functionality.

| Area | Task | Technical Detail |
| :--- | :--- | :--- |
| Scalability | Horizontal Scaling | Evaluate scaling Meilisearch horizontally (clustering) and implementing index sharding for very large book collections. Scale PostgreSQL with read replicas. Implement queue worker auto-scaling based on job backlog. |
| Functionality | Multi-Index Search (Federated Search) | Expand search to include multiple indexes (Books, Authors, Topics, Annotations) with unified results. Implement result merging with relevance normalization across different content types. |
| Functionality | Advanced Export Features | Add endpoints for bulk export of search results in various formats (JSON, CSV, PDF with highlighted sections). Useful for research and analysis use cases. |
| Maintenance | API Versioning | Adopt API versioning (`/api/v1/books/...`), making it easier to introduce future architectural changes without breaking existing clients. v1 maintains current contract, v2 introduces enhanced features. |
| Functionality | Real-time Search (WebSockets) | Implement real-time search using Laravel Echo and WebSockets, providing instant results as users type. Consider implementing search result streaming for large result sets. |
| Performance | CDN Integration | For public content, integrate CDN caching for API responses to reduce latency for geographically distributed users. |

## Key Technical Decisions

### Why Form Requests?
Using `SearchBookRequest` instead of inline validation provides:
- Better separation of concerns
- Reusable validation logic
- Easier to test and maintain
- Clear documentation of API contracts

### Why Differentiated Rate Limiting?
Search operations are I/O intensive (Meilisearch queries), while cached reads are extremely cheap (Redis lookups). Different limits optimize for both user experience and infrastructure protection.

### Why Log Search Queries?
Search logs provide invaluable insights:
- Identify most common queries → optimize ranking for those terms
- Detect zero-result queries → add synonyms or improve content
- Monitor for abuse patterns → adjust rate limiting
- Track performance metrics → identify slow queries

### Why Dedicated Meilisearch Configuration Command?
Running configuration as an Artisan command instead of in every application boot:
- Reduces request overhead
- Makes configuration changes explicit and auditable
- Easier to debug and test
- Aligns with Laravel conventions

## Presentation Outline

This is a brief outline of the topics I will cover during the final presentation:

- **Hands-on Solution**: A live demo of the API endpoints
  - Search with highlighting and pagination
  - Full page retrieval with caching
  - Health check monitoring
  - Rate limiting demonstration

- **Technical Deep Dive**:
  - **Meilisearch Choice**: Why Meilisearch over Elasticsearch/Algolia (developer experience, performance, cost)
  - **Challenge Overcome**: Bypassing the `withPending()` compatibility issue using native Meilisearch client access
  - **API Architecture**: Form Request validation, Resource transformation, array vs object handling for `_formatted` metadata
  - **Advanced Features**: Pagination implementation, relevance scoring exposure, health monitoring
  - **Performance Strategy**: Redis caching layers, differentiated rate limiting, asynchronous indexing

- **Trade-offs and Decisions**:
  - No UI implementation (backend-first approach)
  - Public endpoints (authentication in roadmap)
  - Fixed pagination size (could be configurable)
  - Local logging (would use centralized logging in production)

- **Think Big**:
  - Month 1: Search relevance and analytics
  - Month 2: Production hardening and observability
  - Month 3: Scaling and advanced features

- **Code Quality Highlights**:
  - Comprehensive test coverage
  - Clean separation of concerns
  - Laravel best practices
  - Production-ready patterns (health checks, logging, caching)