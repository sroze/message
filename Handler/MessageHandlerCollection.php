<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Message\Handler;

/**
 * Represents a collection of message handlers.
 *
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
class MessageHandlerCollection
{
    /**
     * @var callable[]
     */
    private $handlers;

    /**
     * @param callable[] $handlers
     */
    public function __construct(array $handlers)
    {
        if (empty($handlers)) {
            throw new \InvalidArgumentException('A collection of message handlers requires at least one handler');
        }

        $this->handlers = $handlers;
    }

    public function __invoke($message)
    {
        return array_map(function ($handler) use ($message) {
            return $handler($message);
        }, $this->handlers);
    }
}
