<?php

namespace App\Classes\Base;


use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class BasePusher implements WampServerInterface
{
    protected $subscribers = [];

    public function getSubscribers()
    {
        return $this->subscribers;
    }

    public function addSubscriber($subscriber)
    {
        $this->subscribers[$subscriber->getId()] = $subscriber;
    }

    public function onSubscribe(ConnectionInterface $conn, $topic)
    {
        $this->addSubscriber($topic);
    }
    public function onUnSubscribe(ConnectionInterface $conn, $topic)
    {
    }
    public function onOpen(ConnectionInterface $conn)
    {
        echo 'connection ';
    }
    public function onClose(ConnectionInterface $conn)
    {
    }
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params)
    {
        // In this application if clients send data it's because the user hacked around in console
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
    {
        // In this application if clients send data it's because the user hacked around in console
        $conn->close();
    }
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo $e->getMessage();
        $conn->close();
    }
}