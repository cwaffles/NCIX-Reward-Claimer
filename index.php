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
  </head>
  <body>
    <?php if($valid_email === FALSE): ?>
        <div style="color: #FFF; background: red; padding: 5px; margin: 5px; font-size: 1.2em; text-align: center;">Invalid Email Address!</div>
    <?php endif; ?>
    <?php if($valid_captcha === FALSE): ?>
        <div style="color: #FFF; background: red; padding: 5px; margin: 5px; font-size: 1.2em; text-align: center;">Invalid Captcha!</div>
    <?php endif; ?>
        
    <?php if($user_created === TRUE): ?>
      <div style="color: #FFF; background: green; padding: 5px; margin: 5px; font-size: 1.2em; text-align: center;"> <?php echo $email; ?> has been added to the auto-claimer!</div>
    <?php elseif($user_created === FALSE): ?>
      <div style="color: #FFF; background: red; padding: 5px; margin: 5px; font-size: 1.2em; text-align: center;">Failed to add you to the auto claimer!</div>
    <?php else: ?>
      <form action="" method="POST">
        Email: <input type="text" name="email" /><br />
        <?php
          require_once('recaptchalib.php');
          $publickey = "6LdUjcISAAAAAHmDqSBdZd9_jgCLpeJ3gHqXrRg4";
          echo recaptcha_get_html($publickey);
        ?>
        <input type="submit" value="Add to List" />
      </form>

    <?php endif; ?>
  </body>
</html>