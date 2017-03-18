<?php
/**
 * All rights reserved. No part of this code may be reproduced, modified,
 * amended or retransmitted in any form or by any means for any purpose without
 * prior written consent of Mizmoz Limited.
 * You must ensure that this copyright notice remains intact at all times
 *
 * @copyright Copyright (c) Mizmoz Limited 2017. All rights reserved.
 */

namespace Mizmoz\Validate\Validator;

use Mizmoz\Validate\Contract\Result as ResultContract;
use Mizmoz\Validate\Contract\Validator;
use Mizmoz\Validate\Helper\WithGuzzleTrait;
use Mizmoz\Validate\Result;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;

/**
 * Validate the reCAPTCHA response
 * @package Mizmoz\Validate\Validator
 */
class IsReCaptcha implements Validator
{
    use WithGuzzleTrait;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $remoteIp;

    /**
     * @var string
     */
    private $url;

    /**
     * ReCaptcha constructor.
     * @param string $secret
     * @param string $remoteIp
     * @param string $url
     */
    public function __construct(
        string $secret,
        string $remoteIp = '',
        string $url = 'https://www.google.com/recaptcha/api/siteverify'
    ) {
        $this->secret = $secret;
        $this->remoteIp = $remoteIp;
        $this->url = $url;
    }

    /**
     * @inheritDoc
     */
    public function validate($value): ResultContract
    {
        $isValid = ($value instanceof ValueWasNotSet);

        if (! $isValid) {
            // value was passed so check it's correct
            if ($this->validateResponse($value)) {
                $isValid = true;
            }
        }

        return new Result(
            $isValid,
            $value,
            'ReCaptcha',
            (! $isValid ? 'Response is invalid' : '')
        );
    }

    /**
     * Validate the recaptcha response
     *
     * @param $value
     * @return bool
     */
    private function validateResponse($value): bool
    {
        $response = $this->getClient()->request('POST', $this->url, [
            'form_params' => [
                'secret' => $this->secret,
                'response' => $value,
                'remoteip' => $this->remoteIp,
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            // failed!
            return false;
        }

        // decode the response and check the success status
        return \GuzzleHttp\json_decode($response->getBody()->getContents())->success;
    }
}
