<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Message\Asynchronous\Routing;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
class SenderLocator implements SenderLocatorInterface
{
    /**
     * Mapping describing which sender should be used for which message.
     *
     * @var array
     */
    private $messageToSenderMapping;

    public function __construct(array $messageToSenderMapping)
    {
        $this->messageToSenderMapping = $messageToSenderMapping;
    }

    /**
     * {@inheritdoc}
     */
    public function getSendersForMessage($message): array
    {
        return $this->messageToSenderMapping[get_class($message)] ?? $this->messageToSenderMapping['*'] ?? array();
    }
}
