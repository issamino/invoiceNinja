<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
$connection = new AMQPConnection('192.168.1.2', 5672, 'admin', 'admin124');
$channel = $connection->channel();
$channel->queue_declare('Invoices', false, true, false, false);
echo ' [*] Waiting for receiving invoices. To exit press CTRL+C', "\n";
$callback = function($msg) {
  echo " [x] Received ", $msg->body, "\n";
};
$channel->basic_consume('Invoices', '', false, true, false, false, $callback);
while(count($channel->callbacks)) {
    $channel->wait();
}
$channel->close();
$connection->close();
?>