<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Helper;

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
