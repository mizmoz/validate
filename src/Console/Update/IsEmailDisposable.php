<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Console\Update;

use \DateTime;
use Mizmoz\Validate\Helper\WithGuzzleTrait;
use Mizmoz\Validate\Exception\RuntimeException;

class IsEmailDisposable
{
    use WithGuzzleTrait;

    /**
     * Default list of urls to grab hosts from
     */
    const DEFAULT_URLS = [
        'https://raw.githubusercontent.com/martenson/disposable-email-domains/master/disposable_email_blocklist.conf',
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
        if (! ($fpHosts = fopen($fileName, 'w'))) {
            throw new RuntimeException('Can‘t open file for writing: ' . $fileName);
        }

        // sort and remove duplicates
        $hosts = array_unique($hosts);
        asort($hosts);

        fputs($fpHosts, "<?php\n\n");
        fputs($fpHosts, '// Last updated: ' . (new DateTime())->format(DateTime::RSS) . "\n\n");
        fputs($fpHosts, "return [\n");

        foreach ($hosts as $host) {
            fputs($fpHosts, "  '" . sha1(strtolower(trim($host))) ."' => '" . $host . "',\n");
        }

        fputs($fpHosts, "];\n");

        fclose($fpHosts);

        return true;
    }
}
