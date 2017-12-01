<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Message\Transport\Serialization;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
interface EncoderInterface
{
    /**
     * Encode a message to a common format understandable by adapters. The encoded array should only
     * contain scalar and arrays.
     *
     * The most common keys of the encoded array are:
     * - `body` (string) - the message body
     * - `headers` (string<string>) - a key/value pair of headers
     *
     * @param object $message
     *
     * @return array
     */
    public function encode($message): array;
}
