<?php

  class SQLQuery {
    
    public $query = false;
    public $error = false;
    public $result = false;
    
    public function __construct($query) {
      $this->query = $query;
      $this->execute();
    }
    
    public function execute() {
      $result = mysql_query($this->query);

      // Invalid Query
      if(!$result) {
        $this->error = 'Invalid query: ' . mysql_error();
        return;
      }

      // Successfull Inserts / Deletes / Ect.
      if($result === TRUE) {
        return;
      } else {
        $this->error = "Error: " . mysql_error();
        return;
      }
      
      // Query returned nothing...
      if(mysql_num_rows($result) == 0) {
        $this->error = "No rows found";
        return;
      }

      while($row = mysql_fetch_assoc($result)) {
        $this->result[] = $row;
      }

    }
  }


?>