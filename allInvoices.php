<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once '/PSWebServiceLibrary.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPConnection('192.168.1.2', 5672, 'admin', 'admin124');
$channel = $connection->channel();

$channel->queue_declare('allInvoices', false, true, false, false);

  $webService = new PrestaShopWebService('http://10.3.51.42/hotel2/intourist/','2YJEYH5SF96CRBB97VR5CRT37KDZG4XZ',false);
    $invoices['resource'] = 'order_invoices';
    $xml = $webService->get($invoices);
	$resources = $xml->children()->children();


$data = json_encode($resources);

$msg = new AMQPMessage($data, array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
$channel->basic_publish($msg, '', 'allInvoices');

//header('Location: form.php?sent=true');

echo " [x] Sent ", $data, "\n";


$channel->close();
$connection->close();

?>

