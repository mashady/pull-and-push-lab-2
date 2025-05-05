<?php
require dirname(__DIR__) . '/vendor/autoload.php'; 

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class StockServer implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        echo "New connection! ({$conn->resourceId})\n";
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = $this->generateStockData();
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($data);  
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }

    private function generateStockData() {
        $symbols = ["AAPL", "GOOG", "AMZN", "MSFT", "TSLA"];
        $symbol = $symbols[array_rand($symbols)];
        $price = rand(100, 1500) + rand(0, 99) / 100;
        return json_encode(['symbol' => $symbol, 'price' => $price, 'timestamp' => date("Y-m-d H:i:s")]);
    }
}

$server = new Ratchet\App('localhost', 8080);
$server->route('/stocks', new StockServer, ['*']);
$server->run();
