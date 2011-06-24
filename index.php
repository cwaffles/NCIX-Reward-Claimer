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
   
 	$active_users = new SQLQuery("SELECT count(*) FROM users WHERE active = 1");
 	
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
	
		<script type="text/javascript">
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-13004092-5']);
			_gaq.push(['_trackPageview']);

		  (function() {
		      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
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

		<p>NCIX.com has a reward point system. Each week a newsletter gets sent out with a link to collect 25 points. It's an easy 25 points, but I got tired of clicking it and entering my email to collect my points. I visit the sale page multiple times each week anyways, and tend to read all my email on my phone. Following that link was nuisance.</p>
		
		<p>I wrote this for myself and a few friends originally. Everyone liked it, so I cleaned it up and released it to the public mid 2009. People liked it and we sat around 266 users for the next 18 months. I rewrote it re-released it with some upgrades to the system (mostly back end) in March 2011, since then we've grown to <?php echo $active_users->result[0]['count(*)']; ?> active users.<p>


		<p>Add your email, sit back, and never collect your newsletter points again. If you're scared I'm collecting your email addresses for malicious purposes then I rather you not use this anyways as you're most likely paranoid and will blame me for any email related problems from here on out. I have no uses for random people's email addresses. But thats your choice.</p>

		<p>
		<h1>FAQ</h1>
		<dl>
			<dt>Can I unsubscribe?</dt>
				<dd>Not right now. But why would you want to? I'll work on implementing this in the future so you can feel warm and fuzzy you can be removed at any point in time</dd>

			<dt>How can I change my email address?</dt>
				<dd>You can't. Just add your new one. If/when I get around to adding in an unsubscribe feature you can remove your old one that way.</dd>

			<dt>It's not collecting points for me</dt>
				<dd>Then you did something wrong. Try re-adding your email, maybe you had a typo originally. You still need to be subscribed to receive NCIX's news letter so your account can receive the points. You also have to be REGISTERED on NCIX.com. You'd be surprised how many people sign up and aren't even registered at NCIX.com</dd>

			<dt>Can I contact you?</dt>
				<dd>I'd prefer it if you don't, but <a href="mailto:ncixrewards@etylogic.com">if you insist</a>.</dd>

		</dl>

    </div>
  </body>
</html>
