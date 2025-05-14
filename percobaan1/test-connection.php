<?php 
$pdo = require 'connection.php'; 
$statement = $pdo->query('select * from users'); 
print_r($statement->fetchAll());