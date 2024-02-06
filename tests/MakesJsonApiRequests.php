<?php

namespace Tests;

use Closure;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\ExpectationFailedException;
use Illuminate\Support\Str;

trait MakesJsonApiRequests
{
    protected bool $formatJsonApiDocument = true;

    protected function setUp(): void
    {
        parent::setUp();

        TestResponse::macro(
            'assertJsonApiValidationErrors',
            $this->assertJsonApiValidationErrors()
        );
    }

    protected function withoutJsonApiDocumentFormatting(): void
    {
        $this->formatJsonApiDocument = false;
    }

    protected function assertJsonApiValidationErrors(): Closure
    {
        return function ($attribute) {
            /** @var TestResponse $this */            

            $pointer = Str::of($attribute)->startsWith('data') 
                ? "/".str_replace('.', '/', $attribute)
                : "/data/attributes/{$attribute}";

            try {
                $this->assertJsonFragment([
                    'source' => ['pointer' => $pointer]
                ]);
                
            } catch (ExpectationFailedException $e) {
                PHPUnit::fail(
                    "Faild to find a JSON:API validation error for key: {$attribute}."
                    .PHP_EOL.PHP_EOL.
                    $e->getMessage()
                );
            }

            try {
                $this->assertJsonStructure([
                    'errors' => [
                        ['title', 'detail', 'source' => ['pointer']]
                    ]
                ]);
            } catch (ExpectationFailedException $e) {
                PHPUnit::fail(
                    "Faild to find a valid JSON:API errors response"
                    .PHP_EOL.PHP_EOL.
                    $e->getMessage()
                );
            }     
            
            $this->assertHeader(
                'Content-Type', 'application/vnd.api+json'
            );

            $this->assertStatus(422);
        };
    }

    protected function getFormattedData($uri, $data): array 
    {
        $path = parse_url($uri)['path'];
        $type = (string) Str::of($path)->after('/api/v1/')->before('/');
        $id = (string) Str::of($path)->after($type)->replace('/', '');

        return [
            'data' => array_filter([
                'type' => $type,
                'id' => $id,
                'attributes' => $data
            ])
        ];
    }

	public function json($method, $uri, array $data = [], array $headers = [], $options = 0)
    {
        $headers['Accept'] = 'application/vnd.api+json';

        if($this->formatJsonApiDocument) 
            $formattedData = $this->getFormattedData($uri, $data);
        
        return parent::json($method, $uri, $formattedData ?? $data, $headers, $options);
    }

    public function postJson($uri, array $data = [], array $headers = [], $options = 0)
    {
        $headers['Content-Type'] = 'application/vnd.api+json';

        return parent::postJson($uri, $data, $headers, $options);
    }

    public function patchJson($uri, array $data = [], array $headers = [], $options = 0)
    {
        $headers['Content-Type'] = 'application/vnd.api+json';

        return parent::patchJson($uri, $data, $headers, $options);
    }
}