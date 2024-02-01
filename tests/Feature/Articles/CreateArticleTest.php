<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use App\Models\Article;

use function PHPSTORM_META\type;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function can_create_article(): void
    {
        $this->withoutExceptionHandling();
        
        $response = $this->postJson(route('api.v1.articles.store'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'My new article',
                    'slug' => 'my-new-article',
                    'content' => 'My new article content'
                ]
            ]
        ]);
        
        $response->assertCreated();

        $article = Article::first();

        $response->assertHeader(
            'Location',
            route('api.v1.articles.show', $article)
        );
    
        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => 'My new article',
                    'slug' => 'my-new-article',
                    'content' => 'My new article content'
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
        $response = $this->postJson(route('api.v1.articles.store'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'slug' => 'my-new-article',
                    'content' => 'My new article content'
                ]
            ]
        ]);
        
        $response->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required(): void
    {
        $response = $this->postJson(route('api.v1.articles.store'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'My new article',
                    'content' => 'My new article content'
                ]
            ]
        ]);
        
        $response->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required(): void
    {
        $response = $this->postJson(route('api.v1.articles.store'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'My new article',
                    'slug' => 'my-new-article',
                ]
            ]
        ]);
        
        $response->assertJsonApiValidationErrors('content');
    }

    /** @test */
    public function content_must_be_a_string(): void
    {
        $response = $this->postJson(route('api.v1.articles.store'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'My new article',
                    'slug' => 'my-new-article',
                    'content' => 123
                ]
            ]
        ]);
        
        $response->assertJsonApiValidationErrors('content');
    }

    /** @test */
    public function title_must_be_at_least_3_characters(): void
    {
        $response = $this->postJson(route('api.v1.articles.store'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'ab',
                    'slug' => 'my-new-article',
                    'content' => 'My new article content'
                ]
            ]
        ]);
        
        $response->assertJsonApiValidationErrors('title');
    }


}
