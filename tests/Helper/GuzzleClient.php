<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Helper;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class GuzzleClient
{
    public static function get(Response $response): ClientInterface
    {
        $mockHandler = new MockHandler([
            $response,
        ]);

        return new Client([
            'handler' => $mockHandler
        ]);
    }

    /**
     * Get the client for a json response
     *
     * @param $response
     * @param int $responseCode
     * @param array $responseHeaders
     * @return ClientInterface
     */
    public static function getWithJsonResponse(
        $response,
        $responseCode = 200,
        $responseHeaders = []
    ): ClientInterface {
        return static::get(
            new Response($responseCode, $responseHeaders, \GuzzleHttp\json_encode($response))
        );
    }
}
