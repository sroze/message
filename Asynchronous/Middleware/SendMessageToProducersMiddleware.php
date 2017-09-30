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

use Symfony\Component\Message\Asynchronous\ConsumedMessage;
use Symfony\Component\Message\Asynchronous\Routing\ProducerForMessageResolverInterface;
use Symfony\Component\Message\MessageBusMiddlewareInterface;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
class SendMessageToProducersMiddleware implements MessageBusMiddlewareInterface
{
    /**
     * @var ProducerForMessageResolverInterface
     */
    private $producerForMessageResolver;

    public function __construct(ProducerForMessageResolverInterface $producerForMessageResolver)
    {
        $this->producerForMessageResolver = $producerForMessageResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($message, callable $next)
    {
        if ($message instanceof ConsumedMessage) {
            $message = $message->getMessage();
        } elseif (!empty($producers = $this->producerForMessageResolver->getProducersForMessage($message))) {
            foreach ($producers as $producer) {
                $producer->produce($message);
            }

            if (!in_array(null, $producers)) {
                return;
            }
        }

        return $next($message);
    }
}
