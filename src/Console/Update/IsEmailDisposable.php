<?php
/**
 * All rights reserved. No part of this code may be reproduced, modified,
 * amended or retransmitted in any form or by any means for any purpose without
 * prior written consent of Mizmoz Limited.
 * You must ensure that this copyright notice remains intact at all times
 *
 * @copyright Copyright (c) Mizmoz Limited 2017. All rights reserved.
 */

namespace Mizmoz\Validate\Console\Update;

use \DateTime;
use Mizmoz\Validate\Console\WithGuzzleTrait;
use Mizmoz\Validate\Exception\RuntimeException;

class IsEmailDisposable
{
    use WithGuzzleTrait;

    /**
     * Default list of urls to grab hosts from
     */
    const DEFAULT_URLS = [
        'https://raw.githubusercontent.com/martenson/disposable-email-domains/master/disposable_email_blacklist.conf',
        'https://gist.githubusercontent.com/ibrahimlawal/bc9b47b038a4d823e1f85cb5ee6ba597/raw',
        'https://raw.githubusercontent.com/wesbos/burner-email-providers/master/emails.txt',
    ];

    /**
     * @var array
     */
    private $urls = [];

    /**
     * IsEmailDisposable constructor.
     *
     * IsEmailDisposable constructor.
     * @param array $urls
     */
    public function __construct(array $urls = self::DEFAULT_URLS)
    {
        $this->urls = $urls;
    }

    /**
     * Add a url to collect domains from
     *
     * @param $url
     * @return IsEmailDisposable
     */
    public function add($url) : IsEmailDisposable
    {
        $this->urls[] = $url;
        return $this;
    }

    /**
     * Update the file with the latest items
     *
     * @param string $fileName
     * @return int
     */
    public function update(string $fileName) : int
    {
        $hosts = [];

        foreach ($this->urls as $url) {
            $response = $this->getClient()->request('GET', $url);

            if ($response->getStatusCode() !== 200) {
                // failed!
                continue;
            }

            $contents = trim($response->getBody()->getContents());

            if (! $contents) {
                // no content, fail!
                continue;
            }

            $hosts += explode("\n", $contents);
        }

        // write the hosts to disk
        $this->writeHostsToFile($fileName, $hosts);

        return count($hosts);
    }

    /**
     * Write the files to disk
     *
     * @param string $fileName
     * @param array $hosts
     * @return bool
     */
    private function writeHostsToFile(string $fileName, array $hosts) : bool
    {
        if (! ($fp = fopen($fileName, 'w'))) {
            throw new RuntimeException('Can‘t open file for writing: ' . $fileName);
        }

        // sort and remove duplicates
        $hosts = array_unique($hosts);
        asort($hosts);

        fputs($fp, "<?php\n\n");
        fputs($fp, '// Last updated: ' . (new DateTime())->format(DateTime::RSS) . "\n\n");
        fputs($fp, "return [\n");

        foreach ($hosts as $host) {
            fputs($fp, "  '" . sha1(strtolower(trim($host))) ."' => '" . $host . "',\n");
        }

        fputs($fp, "];\n");

        fclose($fp);

        return true;
    }
}
