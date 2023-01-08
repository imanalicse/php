Round-robin dispatching:
By default, RabbitMQ will send each message to the next consumer, in sequence.
On average every consumer will get the same number of messages.
This way of distributing messages is called round-robin.