<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Message\Asynchronous\Routing;

use Symfony\Component\Message\Transport\SenderInterface;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
interface SenderLocatorInterface
{
    /**
     * Get the producer (if applicable) for the given message object.
     *
     * @param object $message
     *
     * @return SenderInterface[]
     */
    public function getSendersForMessage($message): array;
}
