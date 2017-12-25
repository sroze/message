<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Message\Asynchronous\Middleware;

use Symfony\Component\Message\Asynchronous\Routing\SenderLocatorInterface;
use Symfony\Component\Message\Asynchronous\Transport\ReceivedMessage;
use Symfony\Component\Message\MiddlewareInterface;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
class SendMessageMiddleware implements MiddlewareInterface
{
    private $senderLocator;

    public function __construct(SenderLocatorInterface $senderLocator)
    {
        $this->senderLocator = $senderLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($message, callable $next)
    {
        if ($message instanceof ReceivedMessage) {
            $message = $message->getMessage();
        } elseif (!empty($senders = $this->senderLocator->getSendersForMessage($message))) {
            foreach ($senders as $sender) {
                $sender->send($message);
            }

            if (!in_array(null, $senders)) {
                return;
            }
        }

        return $next($message);
    }
}
