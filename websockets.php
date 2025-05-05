<?php
require __DIR__.'/vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
  protected \SplObjectStorage $clients;

  public function __construct(){
    $this->clients =  new \SplObjectStorage;
  }


  public function onOpen(ConnectionInterface $conn) {
    $this->clients->attach($conn);
    echo "=> {$conn->resourceId} Connected\n";
  }

  public function onMessage(ConnectionInterface $from, $msg) {
    $count = count($this->clients) -1 ;
    echo "=> {$from->resourceId} -> $count peers: $msg\n";

    foreach($this->clients as $client){
      $prefix = ($client === $from) ? "You ({$from->resourceId})" : "Client ({$from->resourceId})";
      $client->send("$prefix: $msg");
    }
  }

  public function onClose(ConnectionInterface $conn) {
    $this->clients->detach($conn);
    echo "=> {$conn->resourceId} Disconnected\n";
  }

  public function onError(ConnectionInterface $conn, \Exception $e) {
    echo "=> {$e->getMessage()}\n";
    $conn->close();
  }
}

$port = 8083;

$server = IoServer::factory(new HttpServer(
  new WsServer(
    new Chat()
  )
), $port);

echo "PHP WebSocket Server is running on port ws://localhost:$port\n";
echo "welcome samir ";
$server->run();