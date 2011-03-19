<?php
  
  class User {
    public $email = false;

    public function __construct($email) {
      $this->email = $email;
      $this->check_for_deactivation();
    }
    
    public function create() {
      $query = new SQLQuery("INSERT INTO users (email) VALUES ('{$this->email}')");
      return ($query->result);
    }
    
    private function check_for_deactivation() {
      $query = new SQLQuery("SELECT DISTINCT email FROM failed_claims WHERE email = '{$this->email}' GROUP BY email HAVING COUNT(*) >= 3");
      
      if($query->result) {
        $this->deactivate();
      } 
    }
    
    public function deactivate() {
      $query = new SQLQuery("UPDATE users SET active = 0 WHERE email = '{$this->email}'");
      return ($query->result);
    }
  
  }
  
?>