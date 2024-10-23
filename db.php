<?php
$host = 'localhost';   
$db   = 'stacjapaliw';  
$user = 'root';        
$pass = '';            

try {
    
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("set names utf8"); 
} catch (PDOException $e) {
   
    die("Błąd połączenia z bazą danych: " . $e->getMessage());
}
?>
