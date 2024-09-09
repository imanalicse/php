<?php

namespace App\SDK\Websocket;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

include_once 'global_config.php';


class MyWebSocketServer implements MessageComponentInterface
{

    function onOpen(ConnectionInterface $conn)
    {
        // TODO: Implement onOpen() method.
    }

    function onClose(ConnectionInterface $conn)
    {
        // TODO: Implement onClose() method.
    }

    function onError(ConnectionInterface $conn, \Exception $e)
    {
        // TODO: Implement onError() method.
    }

    function onMessage(ConnectionInterface $from, $msg)
    {
        // TODO: Implement onMessage() method.
    }
}