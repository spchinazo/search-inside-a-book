<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    protected string $pagesDir = 'exercise-files/Eloquent_JavaScript_pages';

    public function showPage(int $page)
    {
        $file = $this->resolvePagePath($page);
        $exists = File::exists($file);

        if (!$exists) {
            Log::warning('Page not found', ['page' => $page, 'path' => $file]);
        } else {
            Log::info('Showing book page', ['page' => $page]);
        }

        return view('book.page', [
            'page' => $page,
            'exists' => $exists,
        ]);
    }

    public function showPageImage(int $page)
    {
        $file = $this->resolvePagePath($page);

        if (!File::exists($file)) {
            return response('Page not found', 404);
        }

        return response()->file($file, [
            'Content-Type' => 'image/png',
        ]);
    }

    protected function resolvePagePath(int $page): string
    {
        $filename = sprintf('page-%03d.png', $page);
        return storage_path($this->pagesDir . DIRECTORY_SEPARATOR . $filename);
    }
}
