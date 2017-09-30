Message Component
=================

The Message component helps application to send and receive messages to/from other applications or via
message queues.

Resources
---------

  * [Contributing](https://symfony.com/doc/current/contributing/index.html)
  * [Report issues](https://github.com/symfony/symfony/issues) and
    [send Pull Requests](https://github.com/symfony/symfony/pulls)
    in the [main Symfony repository](https://github.com/symfony/symfony)


Documentation
-------------

**Note:** this documentation is to be moved to symfony.com when merging the Component.

### Bus

The bus is used to dispatch and handle messages. MessageBus' behaviour is in its ordered middleware stack. When using
the message bus with Symfony's FrameworkBundle, the following middlewares are configured for you:

1. `LogMessagesMiddleware` (log the processing of your messages)
2. `SendMessageOnProducersBasedOnRoutingMiddleware` (enable asynchronous processing)
3. `CallMessageHandlerMiddleware` (call the registered handle)

```php
$result = $this->get('message_bus')->dispatch(new MyMessage(/* ... */));
```

### Handlers

Once dispatched to the bus, messages will be handled by a "message handler". A message handler is a PHP callable 
(i.e. a function or an instance of a class) that will do the required processing for your message. It _might_ return a
result.

```php
class MyMessageHandler
{
    public function __invoke(MyMessage $message)
    {
        // Message processing...
    }
}
```

```xml
<service id="App\Handler\MyMessageHandler">
    <tag name="message_handler" />
</service>
```

### Asynchronous messages

Using the Message Component is useful to decouple your application but it also very useful when you want to do some
asychronous processing. This means that your application will produce a message to a queuing system and consume this
message later in the background, using a _worker_.

#### Adapters

The communication with queuing system or 3rd parties is for delegated to libraries for now. You can use one of the 
following adapters:

- [PHP Enqueue bridge](https://github.com/sroze/enqueue-bridge) to use one of their 10+ compatible queues such as 
  RabbitMq, Amazon SQS or Google Pub/Sub.

#### Routing

When doing asynchronous processing, the key is to route the message to the right producer. As the routing is
application-specific and not message-specific, the configuration can be made within the `framework.yaml` 
configuration file as well:

```yaml
framework:
    message:
        routing:
            'My\Message\MessageAboutDoingOperationalWork': my_operations_producer
```

Such configuration would only route the `MessageAboutDoingOperationalWork` message to be asynchronous, the rest of the
messages would still be directly handled.

If you want to do route all the messages to a queue by default, you can use such configuration:
```yaml
framework:
    message:
        routing:
            'My\Message\MessageAboutDoingOperationalWork': my_operations_producer
            '*': my_default_producer
```

Note that you can also route a message to multiple producers at the same time:
```yaml
framework:
    message:
        routing:
            'My\Message\AnImportantMessage': [my_default_producer, my_audit_producer]
```

#### Same bus consumer and producer

To allow us to consume and produce messages on the same bus and prevent a loop, the message bus is equipped with the
`SendMessageOnProducersBasedOnRoutingMiddleware` middleware and a `WrappedIntoConsumedMessageConsumer` consumer. 
The consumer will wraps the received messages into `ConsumedMessage` objects and the middleware will not forward
these messages to the producers.


### Your own producer

Using the `MessageProducerInterface`, you can easily create your own message producer. Let's say you already have an
`ImportantAction` message going through the message bus and handled by a handler. Now, you also want to produce this
message as an email.

1. Create your producer

```php
namespace App\MessageProducer;

use Symfony\Component\Message\MessageProducerInterface;
use App\Message\ImportantAction;

class ImportantActionToEmailProducer implements MessageProducerInterface
{
    private $toEmail;
    private $mailer;
    
    public function __construct(\Swift_Mailer $mailer, string $toEmail)
    {
        $this->mailer = $mailer;
        $this->toEmail = $toEmail;
    }
    
    public function produce($message)
    {
        if (!$message instanceof ImportantAction) {
            throw new \InvalidArgumentException(sprintf('Producer only supports "%s" messages', ImportantAction::class));
        }
        
        $this->mailer->send(
            (new \Swift_Message('Important action made'))
                ->setTo($this->toEmail)
                ->setBody(
                    '<h1>Important action</h1><p>Made by '.$message->getUsername().'</p>',
                    'text/html'
                )
        );
    }
}
```

2. Register your producer service

```xml
<service id="app.message_producer.important_action_to_email" class="App\MessageProducer\ImportantActionToEmailProducer">
    <argument type="service" id="mailer" />
    <argument>%to_email%</argument>
</service>
```

3. Route your important message to the producer

```yaml
framework:
    message:
        routing:
            'App\Message\ImportantAction': [app.message_producer.important_action_to_email, ~]
```

**Note:** this example shows you how you can at the same time produce your message and directly handle it using a `null`
(`~`) producer.

### Your own consumer

A consumer is responsible of reading messages from a source and dispatching them to the application. 

Let's say you already proceed some "orders" on your application using a `NewOrder` message. Now you want to integrate with
a 3rd party or a legacy application but you can't use an API and need to use a shared CSV file with new orders.

You will read this CSV file and dispatch a `NewOrder` message. All you need to do is your custom CSV consumer and Symfony will do the rest.

1. Create your consumer

```php
namespace App\MessageConsumer;

use Symfony\Component\Message\MessageConsumerInterface;
use Symfony\Component\Serializer\SerializerInterface;

use App\Message\NewOrder;

class NewOrdersFromCsvFile implements MessageConsumerInterface
{
    private $serializer;
    private $filePath;
    
    public function __construct(SerializerInteface $serializer, string $filePath)
    {
        $this->serializer = $serializer;
        $this->filePath = $filePath;
    }
    
    public function consume() : \Generator
    {
        $ordersFromCsv = $this->serializer->deserialize(file_get_contents($this->filePath), 'csv'); 
        
        foreach ($ordersFromCsv as $orderFromCsv) {
            yield new NewOrder($orderFromCsv['id'], $orderFromCsv['account_id'], $orderFromCsv['amount']);
        }
    }
}
```

2. Register your consumer service

```xml
<service id="app.message_consumer.new_orders_from_csv_file" class="App\MessageConsumer\NewOrdersFromCsvFile">
    <argument type="service" id="serializer" />
    <argument>%new_orders_csv_file_path%</argument>
</service>
```

3. Use your consumer

```bash
$ bin/console message:consume app.message_consumer.new_orders_from_csv_file
```
