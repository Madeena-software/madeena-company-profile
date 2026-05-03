<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicStorageRouteTest extends TestCase
{
    public function test_public_storage_route_streams_from_public_disk(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/example.txt', 'hello madeena');

        $this->get('/storage/products/example.txt')
            ->assertOk()
            ->assertStreamedContent('hello madeena');
    }

    public function test_public_storage_route_rejects_path_traversal(): void
    {
        Storage::fake('public');

        $this->get('/storage/products/../secret.txt')
            ->assertNotFound();
    }
}
