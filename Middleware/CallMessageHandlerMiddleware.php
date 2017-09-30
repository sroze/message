<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Message\Middleware;

use Symfony\Component\Message\MessageBusMiddlewareInterface;
use Symfony\Component\Message\MessageHandlerResolverInterface;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
class CallMessageHandlerMiddleware implements MessageBusMiddlewareInterface
{
    /**
     * @var MessageHandlerResolverInterface
     */
    private $messageHandlerResolver;

    public function __construct(MessageHandlerResolverInterface $messageHandlerResolver)
    {
        $this->messageHandlerResolver = $messageHandlerResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($message, callable $next)
    {
        $handler = $this->messageHandlerResolver->resolve($message);
        $result = $handler($message);

        $next($message);

        return $result;
    }
}
