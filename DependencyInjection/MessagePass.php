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
                $reflection = new \ReflectionClass($container->getDefinition($serviceId)->getClass());

                try {
                    $method = $reflection->getMethod('__invoke');
                } catch (\ReflectionException $e) {
                    throw new RuntimeException(sprintf('Service "%s" should have an `__invoke` function', $serviceId));
                }

                $parameters = $method->getParameters();
                if (1 !== count($parameters)) {
                    throw new RuntimeException(sprintf('`__invoke` function of service "%s" must have exactly one parameter', $serviceId));
                }

                $parameter = $parameters[0];
                if (null === $parameter->getClass()) {
                    throw new RuntimeException(sprintf('The parameter of `__invoke` function of service "%s" must type hint the Message class it handles', $serviceId));
                }
                if (!class_exists($handles = $parameter->getClass()->getName())) {
                    throw new RuntimeException(sprintf('The message class "%s" declared in `__invoke` function of service "%s" does not exist', $handles, $serviceId));
                }

                $priority = isset($tag['priority']) ? $tag['priority'] : 0;
                $handlersByMessage[$handles][$priority][] = new Reference($serviceId);
            }
        }

        foreach ($handlersByMessage as $message => $handlers) {
            krsort($handlersByMessage[$message]);
            $handlersByMessage[$message] = call_user_func_array('array_merge', $handlersByMessage[$message]);
        }

        return $handlersByMessage;
    }
}
