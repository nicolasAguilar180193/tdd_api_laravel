<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Article;
use Tests\TestCase;

class UpdateArcticleTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function can_update_article(): void
    {
        $article = Article::factory()->create();
        
        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Updated title',
            'slug' => 'updated-title',
            'content' => 'Updated content'
        ])->assertOk();

        $response->assertHeader(
            'Location',
            route('api.v1.articles.show', $article)
        );
    
        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => 'Updated title',
                    'slug' => 'updated-title',
                    'content' => 'Updated content'
                ],
                'links' => [
                    'self' => route('api.v1.articles.show', $article)
                ]
            ]
        ]);
    }

    /** @test */
    public function title_is_required(): void
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
        'slug' => 'updated-slug',
        'content' => 'Updated content'
        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required(): void
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Updated title',
            'content' => 'Updated content'
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required(): void
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Updated title',
            'slug' => 'updated-slug'
        ])->assertJsonApiValidationErrors('content');
    }

    /** @test */
    public function content_must_be_a_string(): void
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Updated title',
            'slug' => 'updated-slug',
            'content' => 123
        ])->assertJsonApiValidationErrors('content');
    }

    /** @test */
    public function title_must_be_at_least_3_characters(): void
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'ab',
            'slug' => 'updated-slug',
            'content' => 'Updated content'
        ])->assertJsonApiValidationErrors('title');
    }
}
