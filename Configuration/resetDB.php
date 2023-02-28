<?php
try {
    $mysql_host = "localhost";
    $mysql_database = "Centres";
    $mysql_user = "scott";
    $mysql_password = "tiger";
    $dbh= new PDO("mysql:host=$mysql_host;dbname=$mysql_database", $mysql_user, $mysql_password);
    $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    
    $query = file_get_contents("CentResCreateDB.txt");
    $dbh->exec($query);
    //print("Database Structure Updated");
    
    
    $query = file_get_contents("CentResDBFunctionsAndMethods.txt");
    $dbh->exec($query);
    print("Database Functions Updated");

} catch(PDOException $e) {
    echo $e->getMessage();//Remove or change message in production code
}
?>