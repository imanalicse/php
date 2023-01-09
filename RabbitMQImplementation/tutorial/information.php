Round-robin dispatching:
By default, RabbitMQ will send each message to the next consumer, in sequence.
On average every consumer will get the same number of messages.
This way of distributing messages is called round-robin.

Bindings:
That relationship between exchange and a queue is called a binding.

The core idea in the messaging model in RabbitMQ is that the producer never sends any messages directly to a queue.
Actually, quite often the producer doesn't even know if a message will be delivered to any queue at all.

Instead, the producer can only send messages to an exchange. An exchange is a very simple thing.
On one side it receives messages from producers and the other side it pushes them to queues.