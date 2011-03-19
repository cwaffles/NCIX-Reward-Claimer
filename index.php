<?php
  
  require 'config.php';
  
  if(isset($_POST['claimno']) && isset($_POST['password'])) {
    if($_POST['password'] == 'password') {
      $results = new Claimer($_POST['claimno']); 
    }
  }
  
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