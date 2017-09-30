<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Message\Transport;

use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
class SerializeMessageWithTypeInHeaders implements MessageDecoderInterface, MessageEncoderInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function decode(array $encodedMessage)
    {
        if (empty($encodedMessage['body']) || empty($encodedMessage['headers'])) {
            throw new \InvalidArgumentException('Encoded message should have at least a `body` some `headers`');
        } elseif (empty($encodedMessage['headers']['type'])) {
            throw new \InvalidArgumentException('Encoded message do not have a `type` header');
        }

        return $this->serializer->deserialize($encodedMessage['body'], $encodedMessage['headers']['type'], 'json');
    }

    /**
     * {@inheritdoc}
     */
    public function encode($message): array
    {
        return array(
            'body' => $this->serializer->serialize($message, 'json'),
            'headers' => array(
                'type' => get_class($message),
            ),
        );
    }
}
