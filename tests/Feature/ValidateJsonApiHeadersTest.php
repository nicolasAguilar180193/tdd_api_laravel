<?php

namespace Tests\Feature;

use App\Http\Middleware\ValidateJsonApiHeaders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ValidateJsonApiHeadersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::any('test_route', fn () => 'OK')
            ->middleware(ValidateJsonApiHeaders::class);
    }
    
    /** @test */
    public function accept_header_must_be_present_in_all_requests(): void
    {
        $this->get('test_route')->assertStatus(406);

        $this->get('test_route', [
            'Accept' => 'application/vnd.api+json'
        ])->assertSuccessful();
    }

    /** @test */
    public function content_type_header_must_be_present_in_all_post_requests(): void
    {
        $this->post('test_route', [], [
            'Accept' => 'application/vnd.api+json'
        ])->assertStatus(415);

        $this->post('test_route', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json'
        ])->assertSuccessful();
    }

    /** @test */
    public function content_type_header_must_be_present_in_all_patch_requests(): void
    {
        $this->patch('test_route', [], [
            'Accept' => 'application/vnd.api+json'
        ])->assertStatus(415);

        $this->patch('test_route', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json'
        ])->assertSuccessful();
    }

    /** @test */
    public function content_type_header_must_be_present_in_responses(): void
    {
        $this->get('test_route', [
            'Accept' => 'application/vnd.api+json'
        ])->assertHeader('Content-Type', 'application/vnd.api+json');

        $this->post('test_route', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json'
        ])->assertHeader('Content-Type', 'application/vnd.api+json');

        $this->patch('test_route', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json'
        ])->assertHeader('Content-Type', 'application/vnd.api+json');
    }

     /** @test */
    public function content_type_header_must_not_be_present_in_empty_responses(): void
    {
        Route::any('test_route', fn() => response()->noContent())
            ->middleware(ValidateJsonApiHeaders::class);

        $this->get('test_route', [
            'Accept' => 'application/vnd.api+json'
        ])->assertHeaderMissing('Content-Type');

        $this->post('test_route', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json'
        ])->assertHeaderMissing('Content-Type');

        $this->patch('test_route', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json'
        ])->assertHeaderMissing('Content-Type');

        $this->delete('test_route', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json'
        ])->assertHeaderMissing('Content-Type');
    }

}
