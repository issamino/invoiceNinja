<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once '/PSWebServiceLibrary.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

global $connection;
global $channel;

allInvoices();

function allInvoices()
{

$connection = new AMQPConnection('192.168.1.2', 5672, 'admin', 'admin124');
$channel = $connection->channel();
$channel->queue_declare('Invoices', false, true, false, false);

  $webService = new PrestaShopWebService('http://10.3.51.42/hotel2/intourist/','2YJEYH5SF96CRBB97VR5CRT37KDZG4XZ',false);
    $opt['resource'] = 'order_invoices';	
	$opt['display'] = 'full';
    $xml = $webService->get($opt);
	$resources = $xml->children()->children();
	$invoices = array();
	foreach ($resources as $resource) {
		  $id = (int) $resource->id;
		  $id_order =  $resource->id_order;
		  $total_paid_tax_excl =  $resource->total_paid_tax_excl;
		  $total_paid_tax_incl =  $resource->total_paid_tax_incl;
		  $total_products =  $resource->total_products;
		  $total_shipping_tax_excl =  $resource->total_shipping_tax_excl;
		  $total_shipping_tax_incl =  $resource->total_shipping_tax_incl;
		  $shop_addres =  $resource->shop_addres;
		  $invoice_address = $resource->invoice_address;
		  $delivery_address = $resource->delivery_address;
		  $date_add = $resource->date_add;
		  
		   $invoices[] = array('id' => $id,'id_order' => $id_order,'total_paid_tax_excl' => $total_paid_tax_excl,'total_paid_tax_incl' => $total_paid_tax_incl,'total_products' => $total_products,'total_shipping_tax_excl' => $total_shipping_tax_excl,'total_shipping_tax_incl' => $total_shipping_tax_incl,'shop_addres' => $shop_addres,'invoice_address' => $invoice_address,'delivery_address' => $delivery_address,'date_add' => $date_add);
	}

$data = json_encode($invoices);

$msg = new AMQPMessage($data, array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
$channel->basic_publish($msg, '', 'Invoices');

//header('Location: form.php?sent=true');

echo " [x] Sent ", $data, "\n";


$channel->close();
$connection->close();
}
?>

