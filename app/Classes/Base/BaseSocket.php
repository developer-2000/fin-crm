<?php

namespace App\Classes\Base;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class BaseSocket implements MessageComponentInterface
{
    function onClose(ConnectionInterface $conn)
    {
    }

    function onError(ConnectionInterface $conn, \Exception $e)
    {
    }
    function onMessage(ConnectionInterface $from, $msg)
    {

    }
    function onOpen(ConnectionInterface $conn)
    {

    }
}