<?php

namespace App\OpenAPI;

/**
 * @OA\Info(
 *     title="Search Inside a Book API",
 *     version="1.0.0",
 *     description="API for searching inside books with Meilisearch integration.",
 *     contact={
 *         "name": "API Support",
 *         "email": "support@example.com"
 *     }
 * )
 * @OA\Server(
 *     url="http://localhost:8888",
 *     description="Development Server"
 * )
 * @OA\PathItem(path="/api")
 */
class OpenAPIInfo
{
}