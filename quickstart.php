<?php
error_reporting(0);
if(isset($_GET['import'])|| isset($_GET['code'])){?>
  <?php

  require 'vendor/autoload.php';
  require_once 'config.php';
  /*
  if (php_sapi_name() != 'cli') {
      throw new Exception('This application must be run on the command line.');
  }*/

  /**
   * Returns an authorized API client.
   * @return Google_Client the authorized client object
   */
  function getClient()
  {
      $client = new Google_Client();
      $client->setApplicationName('Gmail API PHP Quickstart');
      $client->setScopes(Google_Service_Gmail::GMAIL_READONLY);
      $client->setAuthConfig('credentials.json');
      $client->setAccessType('offline');
      $client->setPrompt('select_account consent');

      // Load previously authorized token from a file, if it exists.
      // The file token.json stores the user's access and refresh tokens, and is
      // created automatically when the authorization flow completes for the first
      // time.
      $tokenPath = 'token.json';
      if (file_exists($tokenPath)) {
          $accessToken = json_decode(file_get_contents($tokenPath), true);
          $client->setAccessToken($accessToken);
      }

      // If there is no previous token or it's expired.
      if ($client->isAccessTokenExpired()) {
          // Refresh the token if possible, else fetch a new one.
          if ($client->getRefreshToken()) {
              $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
          } else {
              // Request authorization from the user.
              $authUrl = $client->createAuthUrl();

              if (php_sapi_name() == 'cli') {
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                print 'Enter verification code: ';
                $authCode = trim(fgets(STDIN));
              } else{
                ?>
                <!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
  font-family: 'Source Sans Pro', 'Helvetica Neue', Arial, sans-serif;
  color: #34495e;
  -webkit-font-smoothing: antialiased;
  line-height: 1.6em;
}

p {
  margin: 0;
}

.notice {
  position: relative;
  margin: 1em;
  background: #F9F9F9;
  padding: 1em 1em 1em 2em;
  border-left: 4px solid #DDD;
  box-shadow: 0 1px 1px rgba(0, 0, 0, 0.125);
}

.notice:before {
  position: absolute;
  top: 50%;
  margin-top: -17px;
  left: -17px;
  background-color: #DDD;
  color: #FFF;
  width: 30px;
  height: 30px;
  border-radius: 100%;
  text-align: center;
  line-height: 30px;
  font-weight: bold;
  font-family: Georgia;
  text-shadow: 1px 1px rgba(0, 0, 0, 0.5);
}

.info {
  border-color: #0074D9;
}

.info:before {
  content: "i";
  background-color: #0074D9;
}

.success {
  border-color: #2ECC40;
}

.success:before {
  content: "√";
  background-color: #2ECC40;
}

.warning {
  border-color: #FFDC00;
}

.warning:before {
  content: "!";
  background-color: #FFDC00;
}

.error {
  border-color: #FF4136;
}

.error:before {
  content: "X";
  background-color: #FF4136;
}
</style>
</head>
<body>
  <h2>Importing Gmail data to catmakeup table</h2>
  <div class="notice success"><p><strong>Almost There!</strong> <?php printf("Click <a href='\n%s\n'><strong>here</strong></a> to proceed with the import", $authUrl); ?>
    or <a href='quickstart.php'><strong>cancel</strong></a> the import.
  </p> </div>
  <div class="notice info"><p>You will be redirected to your google account</p></div>
  <div class="notice error"><p>Do not login if the app name is not Quickstart</p></div>

</body>
</html>

                <?php

                if(isset($_GET['code'])){
                  $authCode=trim($_GET['code']);
                }
              }


              // Exchange authorization code for an access token.
              $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
              $client->setAccessToken($accessToken);

              // Check to see if there was an error.
              if (array_key_exists('error', $accessToken)) {
                  throw new Exception(join(', ', $accessToken));
              }
          }
          // Save the token to a file.
          if (!file_exists(dirname($tokenPath))) {
              mkdir(dirname($tokenPath), 0700, true);
          }
          file_put_contents($tokenPath, json_encode($client->getAccessToken()));
      }
      return $client;
  }


  // Get the API client and construct the service object.
  $client = getClient();
  $service = new Google_Service_Gmail($client);

  // Print the labels in the user's account.
  $user = 'me';
  $results = $service->users_labels->listUsersLabels($user);

  if (count($results->getLabels()) == 0) {
    print "No labels found.\n";
  } else {
    print "Labels:\n";
    foreach ($results->getLabels() as $label) {

      if($label->getName()=='INBOX'){
        printf("Importing <br>- %s\n", $label->getName());
        $optParams = [];
            $optParams['maxResults'] = 50; // Return Only 50 Messages
            $optParams['labelIds'] = $label->getName(); // Only show messages in Inbox
            $messages = $service->users_messages->listUsersMessages('me',$optParams);
            $lists = $messages->getMessages();
            if(!empty($lists)){
              foreach($lists as $list){
                $messageId = $list->getId(); // Grab first Message
                $optParamsGet = [];
                $optParamsGet['format'] = 'full'; // Display message in payload
                $message = $service->users_messages->get('me',$messageId,$optParamsGet);
                $messagePayload = $message->getPayload();
                $headers = $message->getPayload()->getHeaders();
                $parts = $message->getPayload()->getParts();
                echo "<pre>";

                foreach($headers as $header){
                  if($header->name==="From"){
                  $from = $header->value;
                  $split = explode(' <',   $from);
                  $name = $split[0];
                  if(!empty($split[1])){
                    $email = rtrim($split[1], '>');
                  } else{
                    $email = $name;
                  }
                  //insert inbox data
                  $query = "SELECT * FROM emails WHERE email = '".$email."'";
                  $result = mysqli_query($link, $query);
                  if(mysqli_num_rows($result) == 0){
                    $insert= "insert into emails(full_name,email) values('".$name."','".$email."')";
                    if ($link->query($insert) === TRUE) {
                      //echo "New record created successfully";
                    } else {
                      echo "Error: " . $insert . "<br>" . $link->error;
                    }
                  }
                  //echo "<br>NameFrom:".$name;
                  //echo "<br>EmailFrom:".$email;
                }
                }

                //var_dump($decodedMessage);
                echo "</pre>";
              }
            } else{
              echo "No messages";
            }
      } else if($label->getName()=='SENT'){
        printf("Importing <br>- %s\n", $label->getName());
        $optParams = [];
            $optParams['maxResults'] = 50; // Return Only 50 Messages
            $optParams['labelIds'] = $label->getName(); // Only show messages in Inbox
            $messages = $service->users_messages->listUsersMessages('me',$optParams);
            $lists = $messages->getMessages();
            if(!empty($lists)){
              foreach($lists as $list){
              $messageId = $list->getId(); // Grab first Message
              $optParamsGet = [];
              $optParamsGet['format'] = 'full'; // Display message in payload
              $message = $service->users_messages->get('me',$messageId,$optParamsGet);
              $messagePayload = $message->getPayload();
              $headers = $message->getPayload()->getHeaders();
              $parts = $message->getPayload()->getParts();
              echo "<pre>";

              foreach($headers as $header){
                if($header->name==="To"){
                $To = $header->value;
                $split = explode(' <',   $To);
                $name = $split[0];
                if(!empty($split[1])){
                  $email = rtrim($split[1], '>');
                } else{
                  $email = $name;
                }
                //insert sent data
                $query = "SELECT * FROM emails WHERE email = '".$email."'";
                $result = mysqli_query($link, $query);
                if(mysqli_num_rows($result) == 0){
                  $insert= "insert into emails(full_name,email) values('".$name."','".$email."')";
                  if ($link->query($insert) === TRUE) {
                    echo "New record created successfully";
                  } else {
                    echo "Error: " . $insert . "<br>" . $link->error;
                  }
                }
                //echo "<br>NameTo:".$name;
                //echo "<br>EmailTo:".$email;
              }
              //print_r($headers);
              }

              //var_dump($decodedMessage);
              echo "</pre>";
            }
            } else{
              echo "No messages";
            }
          //  echo "View Imported Data";
      }





    }
  }
  header("location:index.php");

  }


  ?>
  <!DOCTYPE html>
  <html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
   <a class="a-n2" href="quickstart.php?import=true">
       <span></span>
       <span></span>
       <span></span>
       <span></span>
       Import Gmail Data

   </a>
   <style media="screen">
   body{
  min-height: 100vh;
  background: #000;  overflow: hidden;
  margin: 0px;
  padding: 0px;
  display: flex;
  justify-content: center; align-items: center;
  }
  /neon Button/
  .a-n2,.a-n1{ margin-left: 20px;
  text-decoration: none; position: relative;  padding: 15px 30px; overflow: hidden; transition: 0.2s all;  color: #fff; text-transform: uppercase; letter-spacing: 4px;
  }
  .a-n1:hover{
  background:#F4D03F; color: #000;
  box-shadow: 0px 0px 10px #F4D03F, 0px 0px 40px#F4D03F, 0px 0px 80px #F4D03F; transition-delay: 1s;
  }
  .a-n2 span,.a-n1 span{
  position: absolute;
  display: block;
  }
  .a-n1 span:nth-child(1){
  top:0px;
  left:-100%;
  width: 100%;
  height: 2px;
  background: linear-gradient(90deg,transparent,#F4D03F )
  }

  .a-n1:hover span:nth-child(1){

  left:100%;
  transition: 0.5s;
  }


  .a-n1 span:nth-child(3){
  bottom:0px;
  right:-100%;
  width: 100%;
  height: 2px;
  background: linear-gradient(270deg,transparent,#F4D03F )
  }

  .a-n1:hover span:nth-child(3){

  right:100%;
  transition: 0.5s;
  transition-delay: 0.5s;
  }



  .a-n1 span:nth-child(2){
  top:-100%;
  right:0;
  width:  2px;
  height: 100%;
  background: linear-gradient(180deg,transparent,#F4D03F )
  }

  .a-n1:hover span:nth-child(2){
  top:100%;
  transition: 0.5s; transition-delay: 0.25s;
  }

  .a-n1 span:nth-child(4){
  bottom:-100%;
  left:0;
  width:  2px;
  height: 100%;
  background: linear-gradient(360deg,transparent,#F4D03F )
  }

  .a-n1:hover span:nth-child(4){
  bottom:100%;
  transition: 0.5s;  transition-delay: 0.75s;
  }


  .a-n2:hover{
  background:#26a0da; color: #000;
  box-shadow: 0px 0px 10px #26a0da, 0px 0px 40px#26a0da, 0px 0px 80px #26a0da; transition-delay: 0.2s;
  }


  .a-n2 span:nth-child(1){
  top: 0px;
  left: -91%;
  width: 100%;
  height: 2px;
  background: #26a0da;
  }

  .a-n2:hover span:nth-child(1){

  left:0%;
  transition: 0.3s;
  }


  .a-n2 span:nth-child(3){
  bottom: 0px;
  right: -91%;
  width: 100%;
  height: 2px;
  background: #26a0da;
  }

  .a-n2:hover span:nth-child(3){

  right:0%;
  transition: 0.3s;
  }

  .a-n2 span:nth-child(2){
  bottom: -70%;
  right: 0%;
  width: 2px;
  FONT-WEIGHT: 100;
  background: #26a0da;
  height: 100%;
  }

  .a-n2:hover span:nth-child(2){

  bottom:0%;
  transition: 0.3s;
  }



  .a-n2 span:nth-child(4){
  top: -70%;
  left: 0%;
  width: 2px;
  FONT-WEIGHT: 100;
  background: #26a0da;
  height: 100%;
  }

  .a-n2:hover span:nth-child(4){

  top:0%;
  transition: 0.3s;
  }

   </style>

    </body>
  </html>