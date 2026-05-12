<?php
    $server = "localhost";
    $port = 3306;
    $user = "root";
    $password = "";

    try{
        $conn = new PDO("mysql:host=$server; port=$port; dbname=cosmetics_store", $user, $password);
        //echo "connection got";
        $conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(Exception $e){
        echo $e -> getMessage();
    }

?>