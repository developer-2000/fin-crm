<?php

namespace App\Classes;


use App\Classes\Base\BaseSocket;
use App\Models\Order;
use \App\Models\User;
use Ratchet\ConnectionInterface;

class ServerSocket extends BaseSocket
{
    private $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
    }

    function onOpen(ConnectionInterface $conn)
    {
        parent::onOpen($conn);

        $this->clients->attach($conn);

        echo "New connection " . $conn->resourceId . "\n";
    }

    function onMessage(ConnectionInterface $from, $msg)
    {
        parent::onMessage($from, $msg);

        $inputData = json_decode($msg, true);

        if ($inputData['data']) {
            try {
                switch ($inputData['key']) {
                    case ("orderByWeight") : {
                        $orderModel = new Order();
                        $inputData['data'] = $orderModel->getOrdersByWeight($inputData['data']);
                        break;
                    }
                    case ("getCompanyMonitoring") : {
                        $orderModel = new Order();
                        $inputData['countries'] = $orderModel->getCountriesOrders($inputData['data']);
                        $inputData['operators'] = (new User)->getOperatorsData($inputData['data']);
                        break;
                    }
                }
            } catch (\Exception $exception) {
                echo $exception->getMessage() . "\n";
            }
        }

        $data = json_encode($inputData);

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($data);
            }
        }
    }

    function onError(ConnectionInterface $conn, \Exception $e)
    {
        parent::onError($conn, $e);

        echo $e->getMessage();
        $conn->close();
    }

    function onClose(ConnectionInterface $conn)
    {
        parent::onClose($conn);

        $this->clients->detach($conn);
        echo "onClose\n";
    }

}