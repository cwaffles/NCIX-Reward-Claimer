<?php

class SQLSelect {
  
  public $result = FALSE;
  public $error = FALSE;
  public $QUERY = FALSE;
  
  private $SELECT = FALSE;
  private $FROM = FALSE;
  private $WHERE = FALSE;
  
  
  
  public function __construct($query = FALSE) {
    if($query) {
      $this->QUERY = $query;
    }
  }
  
  public function select($sel) {
    if(is_array($sel)) {
      $this->SELECT = implode(", ", $sel);
    } else {
      $this->SELECT = $sel;
    }
    $this->buildQuery();
  }
  
  public function from($frm) {
    $this->FROM = $frm;
    $this->buildQuery();
  }
  
  public function where($whr) {
    if(is_array($whr)) {
      $this->WHERE = implode(' AND ', $whr);
    } else {
      $this->WHERE = $whr;
    }
    
    $this->buildQuery();
  }
  
  private function buildQuery() {
    if($this->SELECT && $this->FROM) {
      $this->QUERY = "SELECT {$this->SELECT} FROM {$this->FROM}";
    
      if($this->WHERE) {
        $this->QUERY .= " WHERE {$this->WHERE}";
      }
    }
  }
  
  public function execute() {
    
    $result = mysql_query($this->QUERY);
    
    if(!$result) {
      $this->error = 'Invalid query: ' . mysql_error();
      return;
    }
    
    if (mysql_num_rows($result) == 0) {
      $this->error = "No rows found";
      return;
    }
    
    while($row = mysql_fetch_assoc($result)) {
      $this->result[] = $row;
    }
    
    mysql_free_result($result);
    
  }
}
?>