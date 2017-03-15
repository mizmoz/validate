<?php
/**
 * All rights reserved. No part of this code may be reproduced, modified,
 * amended or retransmitted in any form or by any means for any purpose without
 * prior written consent of Mizmoz Limited.
 * You must ensure that this copyright notice remains intact at all times
 *
 * @copyright Copyright (c) Mizmoz Limited 2017. All rights reserved.
 */

namespace Mizmoz\Validate\Console;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

trait WithGuzzleTrait
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * Set the client for making requests
     *
     * @param ClientInterface $client
     * @return $this
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Get the client
     *
     * @return ClientInterface
     */
    public function getClient() : ClientInterface
    {
        if (! $this->client) {
            $this->client = new Client();
        }

        return $this->client;
    }
}
