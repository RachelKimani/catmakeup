<?php

require 'vendor/autoload.php';
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
            printf("Open the following link in your browser:\n%s\n", $authUrl);

            if(isset($_GET['code'])){
              $authCode=trim($_GET['code']);
            } else{
                print 'Enter verification code: ';
                $authCode = trim(fgets(STDIN));
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
      printf("<br>- %s\n", $label->getName());
      $optParams = [];
          $optParams['maxResults'] = 15; // Return Only 15 Messages
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
                //here
                echo "<br>NameFrom:".$name;
                echo "<br>EmailFrom:".$email;
              }
              }

              //var_dump($decodedMessage);
              echo "</pre>";
            }
          } else{
            echo "No messages";
          }
    } else if($label->getName()=='SENT'){
      printf("<br>- %s\n", $label->getName());
      $optParams = [];
          $optParams['maxResults'] = 15; // Return Only 15 Messages
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
              // will be sending this details to db instead of echoing them. Same for the ones in inbox
              echo "<br>NameTo:".$name;
              echo "<br>EmailTo:".$email;
            }
            //print_r($headers);
            }

            //var_dump($decodedMessage);
            echo "</pre>";
          }
          } else{
            echo "No messages";
          }
    }





  }
}
