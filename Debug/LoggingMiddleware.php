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

use Symfony\Component\Message\MiddlewareInterface;
use Psr\Log\LoggerInterface;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
class LoggingMiddleware implements MiddlewareInterface
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
        $this->logger->debug('Starting processing message {class}', array(
            'message' => $message,
            'class' => get_class($message),
        ));

        try {
            $result = $next($message);
        } catch (\Throwable $e) {
            $this->logger->warning('An exception occurred while processing message {class}', array(
                'message' => $message,
                'exception' => $e,
                'class' => get_class($message),
            ));

            throw $e;
        }

        $this->logger->debug('Finished processing message {class}', array(
            'message' => $message,
            'class' => get_class($message),
        ));

        return $result;
    }
}
