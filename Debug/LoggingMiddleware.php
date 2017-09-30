<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Message\Debug;

use Symfony\Component\Message\MessageBusMiddlewareInterface;
use Psr\Log\LoggerInterface;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
class LoggingMiddleware implements MessageBusMiddlewareInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($message, callable $next)
    {
        $this->logger->debug('Starting processing message', array(
            'message' => $message,
        ));

        try {
            $result = $next($message);
        } catch (\Throwable $e) {
            $this->logger->warning('Something went wrong while processing message', array(
                'message' => $message,
                'exception' => $e,
            ));

            throw $e;
        }

        $this->logger->debug('Finished processing message', array(
            'message' => $message,
        ));

        return $result;
    }
}
