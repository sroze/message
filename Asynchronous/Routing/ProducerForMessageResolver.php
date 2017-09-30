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
class ProducerForMessageResolver implements ProducerForMessageResolverInterface
{
    /**
     * Mapping describing which producer should be used for which message.
     *
     * @var array
     */
    private $messageToProducerMapping;

    public function __construct(array $messageToProducerMapping)
    {
        $this->messageToProducerMapping = $messageToProducerMapping;
    }

    /**
     * {@inheritdoc}
     */
    public function getProducersForMessage($message): array
    {
        $messageKey = get_class($message);
        if (array_key_exists($messageKey, $this->messageToProducerMapping)) {
            return $this->messageToProducerMapping[$messageKey];
        }

        if (array_key_exists('*', $this->messageToProducerMapping)) {
            return $this->messageToProducerMapping['*'];
        }

        return array();
    }
}
