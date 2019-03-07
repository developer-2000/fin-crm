<?php

namespace App\Classes;


use App\Classes\Base\BasePusher;

class Pusher extends BasePusher
{
    static function sendData($data)
    {
        $context = new \ZMQContext();
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'my pusher');
        $socket->connect("tcp://87.98.153.229:5555");

        $socket->send(json_encode($data));
    }

    public function broadcast($jsonData)
    {
        echo $jsonData;
        $data = json_decode($jsonData, true);
        $subscribers = $this->getSubscribers();
        if (isset($subscribers[$data['topic_id']])) {
            $topic = $subscribers[$data['topic_id']];
            $topic->broadcast($data);
        }
    }
}