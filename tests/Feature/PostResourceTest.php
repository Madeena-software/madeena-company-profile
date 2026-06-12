<?php

namespace Tests\Feature;

use App\Filament\Resources\PostResource;
use App\Filament\Resources\PostResource\Pages\CreatePost;
use App\Filament\Resources\PostResource\Pages\EditPost;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Livewire\Livewire;
use Tests\TestCase;

class PostResourceTest extends TestCase
{
    use DatabaseTruncation;

    public function test_can_render_post_resource_create_page()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $this->get(PostResource::getUrl('create'))->assertSuccessful();
    }

    public function test_can_create_academic_post()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $jsonPayload = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'academic-equation',
                    'attrs' => [
                        'type' => 'academic-equation',
                        'data' => [
                            'latex' => 'E = mc^2',
                            'ref_id' => 'eq-1'
                        ]
                    ]
                ]
            ]
        ];

        Livewire::test(CreatePost::class)
            ->fillForm([
                'title' => 'Test Academic Post',
                'slug' => 'test-academic-post',
                'user_id' => $user->id,
                'content_json' => $jsonPayload,
                'enable_auto_numbering' => true,
                'content_language' => 'id',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Post::class, [
            'title' => 'Test Academic Post',
            'slug' => 'test-academic-post',
            'enable_auto_numbering' => 1,
        ]);

        $post = Post::where('slug', 'test-academic-post')->first();
        $this->assertIsArray($post->content_json);
        $this->assertEquals('E = mc^2', $post->content_json['content'][0]['attrs']['data']['latex']);
    }
}
