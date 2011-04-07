<?php

class Claimer {
  
  public $claim_number = "";
  public $claims_success = 0;
  public $claims_failed = 0;
  public $errors = array();
  public $already_claimed = 0;
    
  private $active_users = array();
  private $current_user = false;


  public function __construct($claim_number) {
    $this->claim_number = mysql_real_escape_string($claim_number);
    $this->get_active_users();
    $this->claim_points();
  }
  
  private function get_active_users() {
    
    $query = new SQLQuery("SELECT email FROM users WHERE active = 1");

    if($query->result) {
      foreach($query->result as $user) {
        $this->active_users[] = $user['email'];
      }
    }

  }
  
  private function claim_points() {

    /* Sometimes you might want to claim multiple times...! 
    if($this->is_claimed() === TRUE) {
      return FALSE;
    }    
    */
    $ch = curl_init();

    foreach ($this->active_users as $user_email) {
      $this->current_user = new User($user_email);
          
      $user_email = urlencode($this->current_user->email);
      
      $fields = "email=$user_email&claimno=" . urlencode($this->claim_number);
      
      $options = array (
          CURLOPT_URL => CLAIM_URL
        , CURLOPT_POST => 2
        , CURLOPT_POSTFIELDS => $fields
        , CURLOPT_RETURNTRANSFER => TRUE
      );

      curl_setopt_array($ch, $options);

      $result = curl_exec($ch);

      $this->parse_results($result);
    }
    
    curl_close($ch);
    
    if(!isset($this->errors["Invalid claim number"])) {
      $this->log_claim_number();  
    }
  }
  
  private function is_claimed() {

    $query = new SQLQuery("SELECT claim_date FROM claim_numbers WHERE claim_number = '{$this->claim_number}'");

    if($query->result) {
      $this->set_error("Already Claimed on {$query->result[0]['claim_date']}.");
      return TRUE;
    }
    return FALSE;
  }
  
  private function parse_results($result) {

    $claim_responses_regex = array (
        "Sorry, you have already claimed the Bonus"
      , "The email address hasn't been subscribed in the NCIX Newsletter\. Do you want to subscribe ncix newsletter\?"
      , "If you want to claim NCIX Newsletter Bonus, you have to Register NCIX\.com first"
      , "Invalid claim number"
    );
    
    $regex_string = "/(" . implode("|", $claim_responses_regex) . ")/";
    
    preg_match($regex_string, $result, $match);
  
    $this->match_action($match);
  }
  
  private function match_action($match) {
    if(!empty($match[0])) {
      if($match[0] == "Invalid claim number") {
        $this->invalid_claim($match[0]);
        return;
      } else if ($match[0] == "Sorry, you have already claimed the Bonus") {
        $this->already_claimed += 1;
        return;
      } else {
        $this->log_failed_claim($match[0]);
        return;
      }
    }
    $this->claims_success += 1;
  }
  
  private function log_failed_claim($error_message) {
    $error_message = mysql_real_escape_string($error_message);
    
    $query = new SQLQuery("INSERT INTO failed_claims (email, error_message, error_date) VALUES ('{$this->current_user->email}', '$error_message', CURDATE())");
    
    $this->claims_failed += 1;
  }
  
  private function set_error($message) {
    $this->errors[$message] = $message;
  }

  
  private function log_claim_number() {
    $query = new SQLQuery("INSERT INTO claim_numbers (claim_number, claim_date) VALUES ('{$this->claim_number}', CURDATE())");
  }
    
}

?>