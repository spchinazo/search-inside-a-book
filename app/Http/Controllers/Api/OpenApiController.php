<?php

namespace App\Http\Controllers\Api;

/**
 * @OA\Info(
 *      title="Search Inside a Book API",
 *      description="API for searching inside books with Meilisearch integration. Provides powerful full-text search, caching, and health monitoring capabilities.",
 *      version="1.0.0",
 *      x={"logo": {"url": "https://laravel.com/img/logomark.min.svg"}},
 *      contact={
 *          "name": "API Support",
 *          "email": "support@example.com"
 *      },
 *      license={
 *          "name": "MIT",
 *          "url": "https://opensource.org/licenses/MIT"
 *      }
 * )
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="API Server"
 * )
 * @OA\PathItem(path="/api")
 *
 * @OA\Schema(
 *     schema="Error",
 *     type="object",
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Resource not found"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="integer",
 *         example=404
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="The given data was invalid."
 *     ),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         @OA\Property(
 *             property="q",
 *             type="array",
 *             @OA\Items(type="string", example="The search query is required.")
 *         )
 *     )
 * )
 */
class OpenApiController
{
    // This controller only serves as a namespace for OpenAPI annotations
}
