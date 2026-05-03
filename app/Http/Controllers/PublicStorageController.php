<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicStorageController extends Controller
{
    public function __invoke(string $path): StreamedResponse
    {
        $path = str_replace('\\', '/', $path);

        if ($path === '' || str_contains($path, "\0") || $this->containsTraversal($path)) {
            abort(404);
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            abort(404);
        }

        return $disk->response($path);
    }

    private function containsTraversal(string $path): bool
    {
        foreach (explode('/', $path) as $segment) {
            if ($segment === '..') {
                return true;
            }
        }

        return false;
    }
}
