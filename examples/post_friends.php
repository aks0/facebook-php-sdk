<?php

// Acknowledgement: based on ./example.php obtained from facebook-php-sdk

require '../src/facebook.php';

$facebook = new Facebook(array(
  'appId'  => '732529943441809',
  'secret' => 'f5f45b466dab73dac0181745d7d26b6e',
));

// Get User ID
$user = $facebook->getUser();

if ($user) {
  try {
    // guarantees that you have a logged in user
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}

?>

<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>post-friends</title>
    <style>
      body {
        font-family: 'Lucida Grande', Verdana, Arial, sans-serif;
      }
      h1 a {
        text-decoration: none;
        color: #3b5998;
      }
      h1 a:hover {
        text-decoration: underline;
      }
    </style>
  </head>
  <body>
    <h2>post-friends</h2>

    <?php if ($user): ?>
      <a href="<?php echo $logoutUrl; ?>">Logout</a>
    <?php else: ?>
      <div>
        Check the login status using OAuth 2.0 handled by the PHP SDK:
        <a href="<?php echo $statusUrl; ?>">Check the login status</a>
      </div>
      <div>
        Login using OAuth 2.0 handled by the PHP SDK:
        <a href="<?php echo $loginUrl; ?>">Login with Facebook</a>
      </div>
    <?php endif ?>

    <?php if ($user): ?>
      <h3>You</h3>
      <img src="https://graph.facebook.com/<?php echo $user; ?>/picture">

      <h3>Your Friends List</h3>
      <?php
         $ret = $facebook->api(array(
	    'method' => 'fql.multiquery',
            'queries' => array(
            	'query1' => 'SELECT uid2 FROM friend WHERE uid1 = '.$user,
           	'query2' => 'SELECT first_name, last_name FROM user '.
			  'WHERE uid IN (SELECT uid2 from #query1)',
	    )));
         $friends = $ret[1]["fql_result_set"];
         $num_friends = count($friends);
	 print_r("Number of friends: " . $num_friends. "<br/><br/>");
	 for($i = 0; $i < $num_friends; $i++) {
	   print_r(($i+1) . ". " . $friends[$i]["first_name"].
	   " ". $friends[$i]["last_name"]."<br/>");
	 }
      ?>
    <?php else: ?>
      <strong><em>You are not Connected.</em></strong>
    <?php endif ?>
  </body>
</html>
