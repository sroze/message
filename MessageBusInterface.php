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
interface MessageBusInterface
{
    /**
     * Handle the message.
     *
     * The bus can return a value coming from handlers, but is not required to do so.
     *
     * @param object $message
     *
     * @return mixed
     */
    public function handle($message);
}
