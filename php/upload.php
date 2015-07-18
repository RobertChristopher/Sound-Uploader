<?php

require('keys.php');  // Include our app keys file

upload(); 

 function upload(){
	  
	$file = file_get_contents($_FILES['fileToUpload']['tmp_name']); // Grab file   
	
	// Specifies Files endpoint
    $url = 'https://api.parse.com/1/files/' . $_FILES['fileToUpload']['name'];   
    
    $headers = array(  
      "Content-Type:" . file_get_contents($_FILES['fileToUpload']['type']),
      "X-Parse-REST-API-Key: " . restKey,
      "X-Parse-Application-Id: " . appId,
    );
   
   $ch = curl_init($url);
   
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
   curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
   curl_setopt($ch, CURLOPT_POSTFIELDS,$file);
   
   $response = json_decode(curl_exec($ch), true);
   
   curl_close($ch);
   
  
   
   if(isset($response['name'])) {    // Checks for 'name' in JSON response
	   if(associate($response['name'])){ 
		   Header('Location: http://localhost/DCXP/Sound-Uploader/index.html');  // Redirect the user
	   }else{
		   echo 'A fatal error has occured.';
	   }
   }
   
}
   
function associate($name) { 
	
    $url = 'https://api.parse.com/1/classes/Phrases';   
    
    $headers = array(  
      "Content-Type: application/json",
      "X-Parse-REST-API-Key: " . restKey,
      "X-Parse-Application-Id: " . appId,
    );
    

   $data = array(
       "Submitter" => $_POST['submitter'],
	   "Title" => $_POST['nameOfFile'],
	   "Audio" => array(
		"name" => $name,
		"__type" => 'File',
	   ),
   );
   
   $ch = curl_init($url);
   
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
   curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
   
   $response = json_decode(curl_exec($ch), true);
   curl_close($ch);
   
   if(isset($response['createdAt'])) return true;   // Validates our response
   
  }
    
    
?>