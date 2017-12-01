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

use Symfony\Component\Message\MiddlewareInterface;
use Symfony\Component\Message\HandlerLocatorInterface;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
class HandleMessageMiddleware implements MiddlewareInterface
{
    /**
     * @var HandlerLocatorInterface
     */
    private $messageHandlerResolver;

    public function __construct(HandlerLocatorInterface $messageHandlerResolver)
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
