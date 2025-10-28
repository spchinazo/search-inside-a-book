<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;

class SwaggerController extends Controller
{
    public function index()
    {
        return view('swagger.index');
    }

    public function json()
    {
        $openapi = [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Search Inside a Book API',
                'version' => '1.0.0',
                'description' => 'API for searching inside books with Meilisearch integration.',
            ],
            'servers' => [
                [
                    'url' => 'http://localhost:8888',
                    'description' => 'Development Server',
                ],
            ],
            'paths' => [
                '/api/books' => [
                    'get' => [
                        'summary' => 'List all books',
                        'description' => 'Retrieves a list of all available books in the system.',
                        'tags' => ['Books'],
                        'responses' => [
                            '200' => [
                                'description' => 'List of books',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data' => [
                                                    'type' => 'array',
                                                    'items' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'id' => ['type' => 'integer', 'example' => 1],
                                                            'title' => ['type' => 'string', 'example' => 'Eloquent JavaScript'],
                                                            'author' => ['type' => 'string', 'example' => 'Marijn Haverbeke'],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                '/api/books/{book}/search' => [
                    'get' => [
                        'summary' => 'Search within a book',
                        'description' => 'Searches for a query term within the pages of a specific book.',
                        'tags' => ['Search'],
                        'parameters' => [
                            [
                                'name' => 'book',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'integer'],
                            ],
                            [
                                'name' => 'q',
                                'in' => 'query',
                                'required' => true,
                                'schema' => ['type' => 'string'],
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Search results',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data' => ['type' => 'array'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                '/api/books/{book}/pages/{pageNumber}' => [
                    'get' => [
                        'summary' => 'Get full page content',
                        'description' => 'Retrieves the complete text content of a specific book page.',
                        'tags' => ['Pages'],
                        'parameters' => [
                            [
                                'name' => 'book',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'integer'],
                            ],
                            [
                                'name' => 'pageNumber',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'integer'],
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Full page content',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['type' => 'object'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                '/api/health' => [
                    'get' => [
                        'summary' => 'Health check endpoint',
                        'description' => 'Checks the health status of all critical services.',
                        'tags' => ['Health'],
                        'responses' => [
                            '200' => [
                                'description' => 'Service status',
                                'content' => [
                                    'application/json' => [
                                        'schema' => ['type' => 'object'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return response()->json($openapi);
    }
}