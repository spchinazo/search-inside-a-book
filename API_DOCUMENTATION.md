# Book Search API Documentation

## Overview

This API provides search functionality for the "Eloquent JavaScript" book, allowing users to search through the book's content and retrieve specific pages.

## Base URL

```
http://localhost:8888/api
```

## Endpoints

### 1. Get Book Information

**GET** `/book`

Returns information about the book.

#### Response

```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Eloquent JavaScript",
    "author": "Marijn Haverbeke",
    "description": "A Modern Introduction to Programming - 3rd Edition",
    "total_pages": 698
  }
}
```

### 2. Search Book Content

**GET** `/search`

Searches through the book's content with ranking and snippets.

#### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `q` | string | Yes | - | Search query |
| `limit` | integer | No | 20 | Number of results per page (max 100) |
| `page` | integer | No | 1 | Page number |

#### Example Request

```
GET /api/search?q=DOM&limit=10&page=1
```

#### Response

```json
{
  "success": true,
  "data": {
    "results": [
      {
        "id": 163,
        "page_number": 164,
        "snippet": "...<mark>DOM</mark> manipulation...",
        "relevance_score": 100,
        "match_position": 1
      }
    ],
    "total": 106,
    "query": "DOM"
  },
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 106
  }
}
```

#### Search Features

- **Relevance Scoring**: Results are ranked by relevance using multiple factors:
  - Exact phrase matches get highest score (100)
  - Partial matches get medium score (80)
  - Word frequency scoring for multiple matches
- **Snippet Generation**: Each result includes a highlighted snippet showing the context
- **Pagination**: Results are paginated for performance
- **Term Highlighting**: Search terms are highlighted with `<mark>` tags

### 3. Get Specific Page

**GET** `/page/{pageId}`

Retrieves the full content of a specific page.

#### Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `pageId` | integer | Yes | The ID of the page to retrieve |

#### Example Request

```
GET /api/page/163
```

#### Response

```json
{
  "success": true,
  "data": {
    "id": 163,
    "page_number": 164,
    "text_content": "Full page content here...",
    "book": {
      "id": 1,
      "title": "Eloquent JavaScript",
      "author": "Marijn Haverbeke"
    }
  }
}
```

## Error Responses

All endpoints return consistent error responses:

```json
{
  "success": false,
  "message": "Error description",
  "data": null
}
```

### Common HTTP Status Codes

- `200` - Success
- `400` - Bad Request (e.g., missing search query)
- `404` - Not Found (e.g., page not found)
- `429` - Too Many Requests (rate limiting)

## Rate Limiting

The API is rate limited to 60 requests per minute per IP address.

## Search Algorithm Details

### Relevance Scoring

The search algorithm uses a multi-factor scoring system:

1. **Exact Phrase Match**: 100 points
2. **Partial Match**: 80 points  
3. **Word Frequency**: 10 points per matching word

### Snippet Generation

- Snippets are 200 characters long
- Context is centered around the best match
- Word boundaries are respected
- Search terms are highlighted with `<mark>` tags

### Performance Optimizations

- Database indexes on `text_content` for fast searching
- Pagination to limit result sets
- Efficient SQL queries with proper ranking

## Example Usage

### JavaScript/Fetch

```javascript
// Search for "DOM"
const response = await fetch('/api/search?q=DOM&limit=5');
const data = await response.json();

// Get specific page
const pageResponse = await fetch('/api/page/163');
const pageData = await pageResponse.json();
```

### cURL

```bash
# Search
curl "http://localhost:8888/api/search?q=JavaScript&limit=10"

# Get page
curl "http://localhost:8888/api/page/163"
```

## Database Schema

### Books Table

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `title` | varchar | Book title |
| `author` | varchar | Book author |
| `isbn` | varchar | ISBN number |
| `description` | text | Book description |
| `created_at` | timestamp | Creation timestamp |
| `updated_at` | timestamp | Update timestamp |

### Book Pages Table

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `book_id` | bigint | Foreign key to books table |
| `page_number` | integer | Page number in the book |
| `text_content` | text | Full text content of the page |
| `created_at` | timestamp | Creation timestamp |
| `updated_at` | timestamp | Update timestamp |

## Testing

Run the test suite:

```bash
./vendor/bin/sail artisan test --filter=SearchTest
```

The test suite covers:
- API endpoint functionality
- Search result ranking
- Pagination
- Error handling
- Term highlighting
