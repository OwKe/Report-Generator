<?php
/*
 * PDO Database Class
 */

class Database {

    private $db_host = DB_HOST;
    private $db_user = DB_USER;
    private $db_pass = DB_PASS;
    private $db_name = DB_NAME;
    private $db_driver = DB_DRIVER;

    private $dbh;
    private $stmt;
    private $error;

    public function __construct(){

        // Set DSN
        $dsn = $this->db_driver.':Server=' . $this->db_host . ';Database=' . $this->db_name;

        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        // Create PDO instance
        try{
            $this->dbh = new PDO($dsn, $this->db_user, $this->db_pass, $options);
        } catch(PDOException $e){
            $this->error = $e->getMessage();
            echo $this->error;
        }
    }

    // Prepare statement with query
    public function query($sql){
        $this->stmt = $this->dbh->prepare($sql);
    }

    // Bind values
    public function bind($param, $value, $type = null){
        if(is_null($type)){
            switch(true){
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }

        $this->stmt->bindValue($param, $value, $type);
    }

    // Execute the prepared statement
    public function execute(){
        return $this->stmt->execute();
    }

    // Get result set as array of objects
    public function resultSet(){
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ); // PDO::FETCH_OBJ Included to keep Raw queries the same in Laravel (which also returns objects)
    }

    // Get single record as object
    public function single(){
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ); // PDO::FETCH_OBJ Included to keep Raw queries the same in Laravel (which also returns objects)
    }

    // Get row count
    public function rowCount(){
        return $this->stmt->rowCount();
    }
}