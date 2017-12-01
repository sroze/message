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
use Symfony\Component\Message\Handler\MessageHandlerCollection;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
class HandlerLocator implements HandlerLocatorInterface
{
    /**
     * Maps a message (its class) to a given handler.
     *
     * @var array
     */
    private $messageToHandlerMapping;

    public function __construct(array $messageToHandlerMapping = array())
    {
        $this->messageToHandlerMapping = $messageToHandlerMapping;
    }

    public function resolve($message): callable
    {
        $messageKey = get_class($message);

        if (!array_key_exists($messageKey, $this->messageToHandlerMapping)) {
            throw new NoHandlerForMessageException(sprintf('No handler for message "%s"', $messageKey));
        }

        $handler = $this->messageToHandlerMapping[$messageKey];
        if ($this->isCollectionOfHandlers($handler)) {
            $handler = new MessageHandlerCollection($handler);
        }

        return $handler;
    }

    private function isCollectionOfHandlers($handler): bool
    {
        return is_array($handler) && array_reduce($handler, function (bool $allHandlers, $handler) {
            return $allHandlers && is_callable($handler);
        }, true);
    }
}
