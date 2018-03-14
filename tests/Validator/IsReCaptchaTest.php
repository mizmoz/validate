<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Tests\Validator;

use Mizmoz\Validate\Tests\Helper\GuzzleClient;
use Mizmoz\Validate\Tests\TestCase;
use Mizmoz\Validate\Validator\Helper\ValueWasNotSet;
use Mizmoz\Validate\Validator\IsReCaptcha;

class IsReCaptchaTest extends TestCase
{
    public function testValidResponse()
    {
        // get the test client
        $client = GuzzleClient::getWithJsonResponse([
            'success' => true,
        ]);

        $reCaptcha = (new IsReCaptcha('test-code'))
            ->setClient($client);

        // valid item
        $this->assertTrue($reCaptcha->validate('test-response')->isValid());

        // check the request looks good
        $request = $client->getConfig('handler')->getLastRequest();

        // get the sent params
        parse_str($request->getBody()->getContents(), $params);

        $this->assertArrayHasKey('secret', $params);
        $this->assertEquals('test-code', $params['secret']);

        $this->assertArrayHasKey('response', $params);
        $this->assertEquals('test-response', $params['response']);

        // test a not set value
        $this->assertTrue((new IsReCaptcha('test-secret'))->validate(new ValueWasNotSet())->isValid());
    }

    /**
     * Test failure
     */
    public function testInvalidResponse()
    {
        // get the test client
        $client = GuzzleClient::getWithJsonResponse([
            'success' => false,
        ]);

        $reCaptcha = (new IsReCaptcha('test-code'))
            ->setClient($client);

        // valid item
        $this->assertFalse($reCaptcha->validate('test-response')->isValid());
    }
}
