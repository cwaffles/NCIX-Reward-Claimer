<?php
  
  class User {
    public $email = false;

    public function __construct($email = false) {
      if($email) {
        $this->email = $email;
      }
    }
    
    public function create() {
      $query = new SQLQuery("INSERT INTO users (email) VALUES ('{$this->email}')");
      return ($query->result);
    }
    
    public function deactivate() {
      $query = new SQLQuery("UPDATE users SET active = 0 WHERE email = '{$this->email}'");
      return ($query->result);
    }
  
  }
  
?>