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

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
interface MessageBusMiddlewareInterface
{
    /**
     * @param object   $message
     * @param callable $next
     *
     * @return mixed
     */
    public function handle($message, callable $next);
}
