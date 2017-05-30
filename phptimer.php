<?php
require_once '/customerAPI.php';
$lastCustomerID = 1;
newCustomers(1);
function sendNewCustomers()
{
   global $lastCustomerID;
    do
    {
        
       $response =  readCustomerById($lastCustomerID);
	// donne le customer id , si aucun user existe avec cette id il redonne false, sinon il te donne le resultat
        if($response != false)
        {
            
     // je regarde si le user a deja un uniek uuid
            if($response->UUID == false)
            {
                /**/
                $master = getMasterUUID($response->email);
                $UUID = $master["UUID"];
                $version = $master["version"];
                UpdateCustomerUUID($lastCustomerID, $UUID,$version);
                $response->UUID = $UUID;
                $response->version = $version;
                $response->toString();
                sendCreateUserCRM($response);
                sendMONITORINGLog($response);
                
            }
           
           $lastCustomerID++; 
        }
    }while ($response != false );
    
    
   
}

/**/
 //readSavedInfos();
 //read the saved info so you dont have to reinit them
while(true)
{
    
    sendNewCustomers();
    sleep(30);//every 30SEconds
}
?>