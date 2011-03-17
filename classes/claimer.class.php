<?php

class Claimer {
  
  public $claim_number = "";
  public $claims_success = 0;
  public $claims_failed = 0;
  public $deactivated_users = 0;
  public $errors = array();
    
  private $claim_url = CLAIM_URL;
  private $active_users = array();
  private $current_email = "";


  public function __construct($claim_number) {
    $this->claim_number = $claim_number;
    $this->get_active_users();
    $this->claim_points();
    $this->clean_users();
  }
  
  private function get_active_users() {
    
    $query = new SQLSelect();
    $query->select("email");
    $query->from("users");
    $query->where("active = 1");
    $query->execute();

    if($query->result) {
      $this->active_users = $query->result;
    }

  }
  
  private function claim_points() {

    if($this->is_claimed() === TRUE) {
      return FALSE;
    }    

    $ch = curl_init();

    foreach ($this->active_users as $user_email) {    
      $this->current_email = $user_email['email'];
      $user_email = urlencode($user_email['email']);
      $fields = "email=$user_email&claimno=" . urlencode($this->claim_number);
      
      $options = array (
          CURLOPT_URL => $this->claim_url
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

    $query = new SQLSelect();
    $query->select("claim_date");
    $query->from("claim_numbers");
    $query->where("claim_number = '{$this->claim_number}'");
    $query->execute();
    

    if($query->result) {
      $this->errors["Already Claimed on {$query->result[0]['claim_date']}."] = "Already Claimed on {$query->result[0]['claim_date']}.";
      return TRUE;
    }
    return FALSE;
  }
  
  private function parse_results($result) {

    $claim_reponses_regex = array (
        "Sorry, you have already claimed the Bonus"
      , "The email address hasn't been subscribed in the NCIX Newsletter\. Do you want to subscribe ncix newsletter\?"
      , "If you want to claim NCIX Newsletter Bonus, you have to Register NCIX\.com first"
      , "Invalid claim number"
    );
    
    $regex_string = "/(" . implode("|", $claim_reponses_regex) . ")/";
    
    preg_match($regex_string, $result, $match);
  
    $this->match_action($match);
  }
  
  private function match_action($match) {
    if(!empty($match[0])) {
      if($match[0] != "Invalid claim number") {
        $this->log_failed_claim($match[0]);
        return;
      } else {
        $this->errors[$match[0]] = $match[0];
        return; 
      }
    }
    $this->claims_success += 1;
  }
  
  private function log_failed_claim($error_message) {
    $error_message = mysql_real_escape_string($error_message);
    $query = "INSERT INTO failed_claims (email, error_message, error_date) VALUES ('{$this->current_email}', '$error_message', CURDATE())";
    mysql_query($query);
    $this->claims_failed += 1;
  }

  private function clean_users() {
    $query = "SELECT fc.email, COUNT(fc.email) FROM failed_claims as fc JOIN users AS users ON fc.email=users.email WHERE users.active = 1 GROUP BY fc.email";
    $res = mysql_query($query);
    while($row = mysql_fetch_assoc($res)) {
        if($row['COUNT(fc.email)'] >= 4) {
          $this->deactivate_email($row['email']);
        }
    }
  }
  
  private function deactivate_email($email) {
    $query = "UPDATE users SET active = 0 WHERE email = '$email'";
    mysql_query($query);
    $this->deactivated_users += 1;
  }
  
  private function log_claim_number() {
    $query = "INSERT INTO claim_numbers (claim_number, claim_date) VALUES ('{$this->claim_number}', CURDATE())";
    mysql_query($query);
  }
    
}

?>