<?php 
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use InvoiceNinja\Config as NinjaConfig;
use InvoiceNinja\Models\Client;
use InvoiceNinja\Models\Invoice;
$id = "";
$client_id ="";

NinjaConfig::setURL('localhost/ninja3/public/api/v1');
NinjaConfig::setToken('hmijpyfnbqobvwnybehswtdheqhqfsxs');

       function addInvoice($client,$price, $date,$product_name,$product_id,$product_price,$product_quantity){
        $invoice = $client->createInvoice();
        $invoice->addInvoiceItem($product_name,$product_id,$product_price,$product_quantity);
        //$invoice->addInvoiceItem('Hotelreservatie', 'Hotel ', $price);
        $invoice->due_date = $date;
        $invoice->save();   
        }

        function addItemToExistingInvoice($invoice_id,$price,$date,$product_name,$product_id,$product_price,$product_quantity){
        $invoice = Invoice::find($invoice_id);
        $invoice->addInvoiceItem($product_name,$product_id,$product_price,$product_quantity);
        $invoice->due_date = $date;
        $invoice->save(); 
        }

        function isJson($string) {
            return ((is_string($string) &&
                    (is_object(json_decode($string)) ||
                    is_array(json_decode($string))))) ? true : false;
        }


function addInvoiceClient($msg_json){
    
if(isJson($msg_json)){

            $json = json_decode($msg_json, true);
			echo var_dump($json);
			foreach ($json as $rslt) 
			{
				$rslt['firstname']."\n";
				$rslt['lastname']."\n";
				$rslt['email']."\n";
				$rslt['total_price']."\n";
				$rslt['date_add']."\n";
				$rslt['id_customer']."\n";
				$rslt['id_invoice']."\n";
				$rslt['shop_address']."\n";
				$rslt['invoice_address']."\n";
				$rslt['betaalwijze']."\n";
				$rslt['invoice_number']."\n";
				$rslt['product_name']."\n";
				$rslt['product_quantity']."\n";
				$rslt['product_price']."\n";
				$rslt['product_id']."\n";
			}

            $firstname = $rslt['firstname'];
            $lastname = $rslt['lastname'];
            $email = $rslt['email'];
            //$name = $firstname . " " . $lastname;
            $price = (float)$rslt['total_price'];
            $date = (string)$rslt['date_add'];
			
            $id_number = $rslt['id_customer'];
            $id_invoice = $rslt['id_invoice'];
            $shop_address = $rslt['shop_address'];
            $betaalwijze = $rslt['betaalwijze'];
            $invoice_number = $rslt['invoice_number'];
            $product_name = $rslt['product_name'];
            $product_quantity = $rslt['product_quantity'];
            $product_price = $rslt['product_price'];
            $product_id = $rslt['product_id'];
		    $invoice_address = $rslt['invoice_address'];
			$str_explode=explode("<br />",$invoice_address);
			$name = $str_explode[0]; // test test 
			$straat = $str_explode[1]; // eje
			$straat2 = $str_explode[2]; // e
			$gemZip = $str_explode[3]; // 55
			$land = $str_explode[4]; // belgium
			$str_explode=explode(" ",$gemZip);
			$postcode = $str_explode[0]; // 55
			$gemeente = $str_explode[1]; // 55
	
    
$mysqli = new mysqli("localhost", "ninja", "ninja", "ninja");

/* Vérification de la connexion */
if ($mysqli->connect_errno) {
    printf("Échec de la connexion : %s\n", $mysqli->connect_error);
    exit();
}
    
$query = "SELECT client_id FROM contacts WHERE email ='".$email."'";


if ($result=mysqli_query($mysqli,$query))
  {
  // Return the number of rows in result set
  $rowcount=mysqli_num_rows($result);
  
        if($rowcount == 0){
            
            $client = new Client($email,$firstname,$lastname,$name);
            $client->save();
            addInvoice($client, $price, $date);
           
        }

else{
                   
            while ($row = mysqli_fetch_row($result)) {
                    $client_id = (int)$row[0];
            }
   
          
            $query2 = "SELECT id FROM invoices WHERE client_id ='".$client_id."' AND is_public = 0";
                    
                   if ($result2=mysqli_query($mysqli,$query2))
                    {
                        
                        // Return the number of rows in result set
                        $rowcount=mysqli_num_rows($result2);
                            var_dump($rowcount);
                        if($rowcount == 1){

                            while ($row= mysqli_fetch_row($result2)) {
                            $id = (int)$row[0];

                            }
                           
                            $client = Client::find($client_id);
                            $client->save();
                            addItemToExistingInvoice($id,$price,$date,$product_name,$product_id,$product_price,$product_quantity);

                        }
                        elseif($rowcount == 0){
                            $client = Client::find($client_id);
                          
                            $client->save();
                            addInvoice($client, $price, $date);
                        }
                        else{
                            echo 'Meerdere facturen';
                        }
                        

                    }
}
mysqli_close($mysqli);
        }
}
                        else
                        {
                        echo " no json object";
                    }

}

?>

