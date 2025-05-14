<?php 
 
namespace config; 
 
use PDO; 
use PDOException; 
 
class Connection
{ 
    public static function make() 
    { 
        try { 
            return new PDO('mysql:host=localhost;dbname=db_koperasi', 'root', ''); 
        } catch (PDOException $e) { 
            die($e->getMessage()); 
        } 
    } 
}