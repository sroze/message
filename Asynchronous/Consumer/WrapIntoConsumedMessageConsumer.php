<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Message\Asynchronous\Consumer;

use Symfony\Component\Message\Asynchronous\ConsumedMessage;
use Symfony\Component\Message\MessageConsumerInterface;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
class WrapIntoConsumedMessageConsumer implements MessageConsumerInterface
{
    /**
     * @var MessageConsumerInterface
     */
    private $decoratedConsumer;

    public function __construct(MessageConsumerInterface $decoratedConsumer)
    {
        $this->decoratedConsumer = $decoratedConsumer;
    }

    public function consume(): \Generator
    {
        foreach ($this->decoratedConsumer->consume() as $message) {
            yield new ConsumedMessage($message);
        }
    }
}
