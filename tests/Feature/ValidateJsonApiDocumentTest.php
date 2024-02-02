<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ValidateJsonApiDocument;
use Tests\TestCase;

class ValidateJsonApiDocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::any('test_route', fn () => 'OK')
            ->middleware(ValidateJsonApiDocument::class);
    }
    
    /** @test */
    public function data_is_required(): void
    {
        $this->postJson('test_route', [])
            ->assertJsonApiValidationErrors('data');

        $this->patchJson('test_route', [])
            ->assertJsonApiValidationErrors('data');
    }

    /** @test */
    public function data_must_be_an_array(): void
    {
        $this->postJson('test_route', ['data' => 'not an array'])
            ->assertJsonApiValidationErrors('data');

        $this->patchJson('test_route', ['data' => 'not an array'])
            ->assertJsonApiValidationErrors('data');
    }

    /** @test */
    public function data_type_is_required(): void
    {
        $this->postJson('test_route', ['data' => [
            'attributes' => ['key' => 'value'],
        ]])->assertJsonApiValidationErrors('data.type');

        $this->patchJson('test_route', ['data' => [
            'attributes' => ['key' => 'value'],
        ]])->assertJsonApiValidationErrors('data.type');
    }

    /** @test */
    public function data_type_must_be_a_string(): void
    {
        $this->postJson('test_route', ['data' => [
            'type' => 123,
            'attributes' => ['key' => 'value'],
        ]])->assertJsonApiValidationErrors('data.type');

        $this->patchJson('test_route', ['data' => [
            'type' => 123,
            'attributes' => ['key' => 'value'],
        ]])->assertJsonApiValidationErrors('data.type');
    }

    /** @test */
    public function data_attributes_is_required(): void
    {
        $this->postJson('test_route', ['data' => [
            'type' => 'test',
        ]])->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('test_route', ['data' => [
            'type' => 'test',
        ]])->assertJsonApiValidationErrors('data.attributes');

    }

    /** @test */
    public function data_attributes_must_be_an_array(): void
    {
        $this->postJson('test_route', ['data' => [
            'type' => 'test',
            'attributes' => 'not an array',
        ]])->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('test_route', ['data' => [
            'type' => 'test',
            'attributes' => 'not an array',
        ]])->assertJsonApiValidationErrors('data.attributes');
    }


    /** @test */
    public function data_id_is_required(): void
    {
        $this->patchJson('test_route', [
            'data' => [
                'type' => 'test',
                'attributes' => ['key' => 'value'],
            ]
        ])->assertJsonApiValidationErrors('data.id');
    }

    /** @test */
    public function data_id_must_be_a_string(): void
    {
        $this->patchJson('test_route', [
            'data' => [
                'type' => 'test',
                'id' => 123,
                'attributes' => ['key' => 'value'],
            ]
        ])->assertJsonApiValidationErrors('data.id');
    }

    /** @test */
    public function only_accepts_valid_json_api_document(): void
    {
        $this->postJson('test_route', [
            'data' => [
                'type' => 'test',
                'attributes' => ['key' => 'value'],
            ]
        ])->assertSuccessful();

        $this->patchJson('test_route', [
            'data' => [
                'type' => 'test',
                'id' => '1',
                'attributes' => ['key' => 'value'],
            ]
        ])->assertSuccessful();
    }
}
