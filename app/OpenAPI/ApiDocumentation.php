<?php

namespace App\OpenAPI;

/**
 * @OA\Get(
 *     path="/api/books",
 *     summary="List all books",
 *     description="Retrieves a list of all available books in the system. Results are cached for 1 hour.",
 *     tags={"Books"},
 *     @OA\Response(
 *         response=200,
 *         description="List of books",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="title", type="string", example="Eloquent JavaScript"),
 *                     @OA\Property(property="author", type="string", example="Marijn Haverbeke"),
 *                     @OA\Property(property="description", type="string", example="A book about JavaScript"),
 *                     @OA\Property(property="search_url", type="string", example="/api/books/1/search"),
 *                     @OA\Property(property="created_at", type="string", format="date-time"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time")
 *                 )
 *             )
 *         )
 *     )
 * )
 */

/**
 * @OA\Get(
 *     path="/api/books/{book}/search",
 *     summary="Search within a book",
 *     description="Searches for a query term within the pages of a specific book. Returns paginated results with highlighted snippets and relevance scores.",
 *     tags={"Search"},
 *     @OA\Parameter(
 *         name="book",
 *         in="path",
 *         required=true,
 *         description="Book ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="q",
 *         in="query",
 *         required=true,
 *         description="Search query (2-200 characters)",
 *         @OA\Schema(type="string", minLength=2, maxLength=200)
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         required=false,
 *         description="Page number (default: 1)",
 *         @OA\Schema(type="integer", minimum=1, default=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Search results with pagination",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="page_number", type="integer", example=5),
 *                     @OA\Property(property="snippet", type="string", example="This is a <em>test</em> snippet"),
 *                     @OA\Property(property="full_page_url", type="string", example="/api/books/1/pages/5"),
 *                     @OA\Property(property="relevance_score", type="number", format="float", example=0.95)
 *                 )
 *             ),
 *             @OA\Property(
 *                 property="meta",
 *                 type="object",
 *                 @OA\Property(property="total", type="integer", example=45),
 *                 @OA\Property(property="per_page", type="integer", example=20),
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="last_page", type="integer", example=3)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Book not found"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 */

/**
 * @OA\Get(
 *     path="/api/books/{book}/pages/{pageNumber}",
 *     summary="Get full page content",
 *     description="Retrieves the complete text content of a specific book page. Results are cached for 1 hour.",
 *     tags={"Pages"},
 *     @OA\Parameter(
 *         name="book",
 *         in="path",
 *         required=true,
 *         description="Book ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="pageNumber",
 *         in="path",
 *         required=true,
 *         description="Page Number",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Full page content",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="page_number", type="integer", example=5),
 *             @OA\Property(property="content", type="string", example="Full page text content..."),
 *             @OA\Property(property="book_title", type="string", example="Eloquent JavaScript")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Page not found"
 *     )
 * )
 */

/**
 * @OA\Get(
 *     path="/api/health",
 *     summary="Health check endpoint",
 *     description="Checks the health status of all critical services: database, cache (Redis), and Meilisearch. Returns 200 if healthy, 503 if any service is unhealthy.",
 *     tags={"Health"},
 *     @OA\Response(
 *         response=200,
 *         description="All services are healthy",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", enum={"healthy", "degraded", "unhealthy"}, example="healthy"),
 *             @OA\Property(property="timestamp", type="string", format="date-time", example="2024-10-27T12:00:00Z"),
 *             @OA\Property(
 *                 property="services",
 *                 type="object",
 *                 @OA\Property(property="database", type="string", enum={"healthy", "unhealthy"}, example="healthy"),
 *                 @OA\Property(property="cache", type="string", enum={"healthy", "unhealthy"}, example="healthy"),
 *                 @OA\Property(property="meilisearch", type="string", enum={"healthy", "unhealthy"}, example="healthy")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=503,
 *         description="One or more services are unhealthy",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", enum={"healthy", "degraded", "unhealthy"}, example="unhealthy"),
 *             @OA\Property(property="timestamp", type="string", format="date-time"),
 *             @OA\Property(
 *                 property="services",
 *                 type="object",
 *                 @OA\Property(property="database", type="string", enum={"healthy", "unhealthy"}, example="unhealthy"),
 *                 @OA\Property(property="cache", type="string", enum={"healthy", "unhealthy"}),
 *                 @OA\Property(property="meilisearch", type="string", enum={"healthy", "unhealthy"})
 *             )
 *         )
 *     )
 * )
 */