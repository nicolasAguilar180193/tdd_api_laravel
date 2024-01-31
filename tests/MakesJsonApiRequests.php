<?php

namespace Tests;

trait MakesJsonApiRequests
{
	public function json($method, $uri, array $data = [], array $headers = [], $options = 0)
    {
        $headers['Accept'] = 'application/vnd.api+json';

        return parent::json($method, $uri, $data, $headers, $options);
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