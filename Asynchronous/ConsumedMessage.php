<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Message\Asynchronous;

/**
 * Wraps a consumed message. This is mainly used by the `SendMessageToProducersMiddleware` middleware to identify
 * a message should not be re-produced if it was just consumed.
 *
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
final class ConsumedMessage
{
    private $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
