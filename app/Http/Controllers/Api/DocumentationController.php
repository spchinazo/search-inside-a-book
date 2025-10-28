<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;

/**
 * @OA\Info(
 *     title="Search Inside a Book API",
 *     version="1.0.0",
 *     description="API for searching inside books with Meilisearch integration."
 * )
 * @OA\Server(
 *     url="http://localhost:8888",
 *     description="Development Server"
 * )
 */

/**
 * @OA\Get(
 *     path="/api/books",
 *     summary="List all books",
 *     description="Retrieves a list of all available books in the system.",
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
 *                     @OA\Property(property="title", type="string", example="Eloquent JavaScript")
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
 *     description="Searches for a query term within the pages of a specific book.",
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
 *         description="Search query",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Search results",
 *         @OA\JsonContent(type="object")
 *     )
 * )
 */

/**
 * @OA\Get(
 *     path="/api/books/{book}/pages/{pageNumber}",
 *     summary="Get full page content",
 *     description="Retrieves the complete text content of a specific book page.",
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
 *         @OA\JsonContent(type="object")
 *     )
 * )
 */

/**
 * @OA\Get(
 *     path="/api/health",
 *     summary="Health check endpoint",
 *     description="Checks the health status of all critical services.",
 *     tags={"Health"},
 *     @OA\Response(
 *         response=200,
 *         description="Service status",
 *         @OA\JsonContent(type="object")
 *     )
 * )
 */
class DocumentationController extends Controller
{
    // This controller only contains OpenAPI annotations
}