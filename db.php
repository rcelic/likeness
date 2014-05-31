<?php

class db {
    public $dbh;
    public $rs;
    public $lastid;

    public function __construct(){
        $this->dbh = new PDO('mysql:host=localhost;dbname=hexagr5_likeness', 'hexagr5_admin', 'likemike12');
    }

    public function Insert($sql,$data){
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($data);
        
    }

    public function Query($sql,$data){
        $stmt = $this->dbh->prepare($sql);
        
        $stmt->execute($data);
        $lastid = $this->dbh->lastInsertId();
        $tmp = $stmt->fetchAll();
        $ans = new rs($tmp);
        return $ans;
                

        
    }

}

class rs {

    public $rs;

    public function __construct($inputRs){
        $this->rs = $inputRs;
    }

    public function numRows(){
        return count($this->rs);
    }

    public function searchRs(){
//        code to search $this->rs for what you want....
    }

    public function returnArray(){
        return $this->rs;
    }

    public function returnArrayColNames(){
//        code to strip out only names for columns
        return $this->rs;
    }
    public function numCols(){
        //return count($this->rs);
    }
    public function returnValue(){
        return $this->rs[0][0];
    }
}

//$rs = $db->Query();
//$arrayData = $rs->returnArray();
//$numRows = $rs->numRows();
//$numCols = $rs->numCols();

?>
