<?php
/**
 * All rights reserved. No part of this code may be reproduced, modified,
 * amended or retransmitted in any form or by any means for any purpose without
 * prior written consent of Mizmoz Limited.
 * You must ensure that this copyright notice remains intact at all times
 *
 * @copyright Copyright (c) Mizmoz Limited 2017. All rights reserved.
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
