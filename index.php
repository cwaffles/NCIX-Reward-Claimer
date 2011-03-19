<?php
  
  require 'config.php';
  require_once('recaptchalib.php');
  
   $privatekey = "6LdUjcISAAAAAEaucmnU214RKiN48ByjRlpuH_Gm";
   if(isset($_POST["recaptcha_challenge_field"]) && $_POST["recaptcha_response_field"]) {
     $resp = recaptcha_check_answer (
          $privatekey
        , $_SERVER["REMOTE_ADDR"]
        , $_POST["recaptcha_challenge_field"]
        , $_POST["recaptcha_response_field"]
      );  
   }
   

  $user_created = "";
  $valid_email = TRUE;
  $valid_captcha = TRUE;
  
  if(isset($_POST['email'])) {
 
    $email = $_POST['email'];
    $valid_email_regex = "/[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}/";
    preg_match($valid_email_regex, $email, $match);
    
    if(isset($match[0])) {
      if($match[0] == $email) {
        if ($resp->is_valid) {
          $user = new User($email);

          if($user->create()){
            $user_created = TRUE;
          } else {
            $user_created = FALSE;
          }
        } else {
          $valid_captcha = FALSE;
        }    
      }
    } else {
      $valid_email = FALSE;
    }
    
  }
  
?>
<!DOCTYPE html>
<html>
  <head>
    <title>NCIX Point Claimer</title>
    <link rel="stylesheet" type="text/css" media="all" href="style.css" />
    <script>
          var RecaptchaOptions = { theme : 'clean' };
    </script>
    
  </head>
  <body>
    <div id="main">
      <?php if($valid_email === FALSE): ?>
          <div class='error'>Invalid Email Address!</div>
      <?php endif; ?>
      <?php if($valid_captcha === FALSE): ?>
          <div class='error'>Invalid Captcha!</div>
      <?php endif; ?>
      <?php if($user_created === FALSE): ?>
        <div class='error'>Failed to add you to the auto claimer!<br />You may already be on the list.</div>
      <?php endif; ?>        
      <?php if($user_created === TRUE): ?>
        <div class='success'> <?php echo $email; ?> has been added to the auto-claimer!</div>
      <?php endif; ?>
    
      <form action="" method="POST">
        Email: <input type="text" name="email" /><br />
        <?php
          require_once('recaptchalib.php');
          $publickey = "6LdUjcISAAAAAHmDqSBdZd9_jgCLpeJ3gHqXrRg4";
          echo recaptcha_get_html($publickey);
        ?>
        <input type="submit" value="Add to List" />
      </form>

    </div>
  </body>
</html>