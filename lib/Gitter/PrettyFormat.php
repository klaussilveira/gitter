<?php

/*
 * This file is part of the Gitter library.
 *
 * (c) Klaus Silveira <klaussilveira@php.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitter;

use Gitter\Constants\Exceptions;
use SimpleXMLIterator;

class PrettyFormat
{
    /**
     * @param string $output
     * @return array|string|null
     */
    public function escapeXml(string $output): array|string|null
    {
        return preg_replace('/[\x00-\x1f]/', '?', $output);
    }

    /**
     * @param string $output
     * @return mixed
     */
    public function parse(string $output): mixed
    {
        if (empty($output)) {
            throw new \RuntimeException(Exceptions::NO_DATA_AVAILABLE);
        }

        try {
            $xml = new \SimpleXmlIterator("<data>$output</data>");
        } catch (\Exception $e) {
            $output = $this->escapeXml($output);
            $xml = new \SimpleXmlIterator("<data>$output</data>");
        }

        $data = $this->iteratorToArray($xml);

        return $data['item'];
    }

    /**
     * @param SimpleXmlIterator $iterator
     * @return array
     */
    protected function iteratorToArray(SimpleXmlIterator $iterator): array
    {
        $data = [];

        foreach ($iterator as $key => $item) {
            if ($iterator->hasChildren()) {
                $data[$key][] = $this->iteratorToArray($item);
                continue;
            }

            $data[$key] = trim(strval($item));
        }

        return $data;
    }
}
