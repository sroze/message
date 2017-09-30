<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Message;

use Symfony\Component\Message\Exception\NoHandlerForMessageException;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
interface MessageHandlerResolverInterface
{
    /**
     * Return the handler for the given message.
     *
     * @param object $message
     *
     * @throws NoHandlerForMessageException
     *
     * @return callable
     */
    public function resolve($message): callable;
}
