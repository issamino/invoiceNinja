<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once '/PSWebServiceLibrary.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;



global $connection;
global $channel;

Invoice();

function Invoice()
{

$connection = new AMQPConnection('192.168.1.2', 5672, 'admin', 'admin124');
$channel = $connection->channel();

$channel->queue_declare('Invoices', false, true, false, false);

 $webService = new PrestaShopWebService('http://10.3.51.42/hotel2/intourist/','2YJEYH5SF96CRBB97VR5CRT37KDZG4XZ',false);
$allIN = array();


$xml2 = $webService->get(array('resource'=>'order_invoices','id'=>1));
$resourceNodes2 = $xml2->children()->children();
  //API: order_invoices
  $id_invoice = (int) $resourceNodes2->id;
  $id_order = (int) $resourceNodes2->id_order;
  $total_price = (float) $resourceNodes2-> total_products_wt;
  $shop_address = (string) $resourceNodes2-> shop_address;
  $invoice_address = (string) $resourceNodes2-> invoice_address;
  $date_add = (string)$resourceNodes2-> date_add;

$xml3 = $webService->get(array('resource'=>'orders', 'id' => $id_order));
$resourceNodes3 = $xml3->children()->children();
   //API: orders
  $id_customer = (int) $resourceNodes3-> id_customer;
  $betaalwijze = (string) $resourceNodes3-> payment;
  $invoice_number = (int) $resourceNodes3-> invoice_number;
  $product_name = (string) $resourceNodes3-> associations->order_rows ->order_row->product_name;
  $product_quantity = (int) $resourceNodes3-> associations->order_rows ->order_row->product_quantity;
  $product_price = (float) $resourceNodes3-> associations->order_rows ->order_row->unit_price_tax_incl;
  $product_id = (int) $resourceNodes3-> associations->order_rows ->order_row->product_id;
  
  

$xml = $webService->get(array('resource'=>'customers', 'id'=> $id_customer));
$resourceNodes = $xml->children()->children();
  //API: customers
  $id = (int) $resourceNodes->id;
  $firstname = (string) $resourceNodes->firstname;
  $lastname =  (string) $resourceNodes->lastname;
  $email =  (string) $resourceNodes->email;

 

  
  $allIN[] = array('id_customer' => $id,'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'id_invoice' => $id_invoice, 'total_price' => $total_price, 'shop_address' => $shop_address, 'invoice_address' => $invoice_address, 'date_add' => $date_add, 'id_customer' => $id_customer, 'betaalwijze' => $betaalwijze, 'invoice_number' => $invoice_number, 'product_name' => $product_name,'product_quantity' => $product_quantity,'product_price' => $product_price,'product_id' => $product_id);


$data = json_encode($allIN);




$msg = new AMQPMessage($data, array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
$channel->basic_publish($msg, '', 'Invoices');

//header('Location: form.php?sent=true');

echo " [x] Sent ", $data, "\n";


$channel->close();
$connection->close();
}
?>

