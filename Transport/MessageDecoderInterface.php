<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Message\Transport;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
interface MessageDecoderInterface
{
    /**
     * Decode the message from an encoded-form. The `$encodedMessage` parameter is a key-value array that
     * describes the message, that will be used by the different adapters.
     *
     * The most common keys are:
     * - `body` (string) - the message body
     * - `headers` (string<string>) - a key/value pair of headers
     *
     * @param array $encodedMessage
     *
     * @return object
     */
    public function decode(array $encodedMessage);
}
