<?php
  
  require 'config.php';
  
  if(isset($_POST['claimno']) && isset($_POST['password'])) {
    if($_POST['password'] == 'ncix2009') {
      $results = new Claimer($_POST['claimno']); 
    }
  }
  
	$total_users = new SQLQuery("SELECT count(*) FROM users");
	$active_users = new SQLQuery("SELECT count(*) FROM users WHERE active = 1");
	
	echo "<p>Total Users: {$total_users->result[0]['count(*)']}<br />";
	echo "Active Users: {$active_users->result[0]['count(*)']}</p>";

?>
<!DOCTYPE html>
<html>
  <head>
    <title>NCIX Point Claimer</title>
  </head>
  <body>
    <?php
      if(isset($results)):
        if(empty($results->errors)):
    ?>
      
      <div style="text-align: center; font-weight: bold; padding: 10px; font-size: 1.2em; color: #FFF; background-color: green; border: 1px solid #000; margin: 10px;">
        <h1>Complete!</h1>
        <ul>
          <li>Successful Claims: <?php echo $results->claims_success; ?></li>
          <li>Failed Claims: <?php echo $results->claims_failed; ?></li>
          <li>Already Claimed: <?php echo $results->already_claimed; ?></li>
        </ul>
      </div>
      
    <?php else: ?>
      
      <div style="text-align: center; font-weight: bold; padding: 10px; font-size: 1.2em; color: #000; background-color: red; border: 1px solid #000; margin: 10px;">
        <h1>ERROR</h1>
        <ul>
          <?php
            foreach ($results->errors as $error) {
              echo "<li>$error</li>";
            }
          ?>
        </ul>
      </div>
    
    <?php
        endif;
      endif;
    ?>
    
    <form action="" method="POST">
      Claim Number: <input type="text" name="claimno" /><br />
      Password: <input type="password" name="password"><br />
      <input type="submit" value="Submit" />
    </form>
  </body>
</html>
