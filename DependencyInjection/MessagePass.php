<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Message\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
class MessagePass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    private $messageHandlerResolverService;
    private $handlerTag;
    private $messageBusService;
    private $middlewareTag;

    public function __construct(string $messageBusService = 'message_bus', string $middlewareTag = 'message_middleware', string $messageHandlerResolverService = 'message.handler_resolver', string $handlerTag = 'message_handler')
    {
        $this->messageHandlerResolverService = $messageHandlerResolverService;
        $this->handlerTag = $handlerTag;
        $this->messageBusService = $messageBusService;
        $this->middlewareTag = $middlewareTag;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$middlewares = $this->findAndSortTaggedServices($this->middlewareTag, $container)) {
            throw new RuntimeException(sprintf('You must tag at least one service as "%s" to use the "%s" service.', $this->middlewareTag, $this->messageBusService));
        }

        $busDefinition = $container->getDefinition($this->messageBusService);
        $busDefinition->replaceArgument(0, $middlewares);

        $handlerResolver = $container->getDefinition($this->messageHandlerResolverService);
        $handlerResolver->replaceArgument(0, $this->findHandlers($container));
    }

    private function findHandlers(ContainerBuilder $container): array
    {
        $handlersByMessage = array();

        foreach ($container->findTaggedServiceIds($this->handlerTag, true) as $serviceId => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['handles'])) {
                    throw new RuntimeException(sprintf('Tag "%s" on service "%s" should have an `handles` attribute', $this->handlerTag, $serviceId));
                }

                $priority = isset($tag['priority']) ? $tag['priority'] : 0;
                $handlersByMessage[$tag['handles']][$priority][] = new Reference($serviceId);
            }
        }

        foreach ($handlersByMessage as $message => $handlers) {
            krsort($handlersByMessage[$message]);
            $handlersByMessage[$message] = call_user_func_array('array_merge', $handlersByMessage[$message]);
        }

        return $handlersByMessage;
    }
}
