<?php
    // makes sure the reset script is accessible if there is an error with the database,
    // there are no employees in the database, or a manager is logged in and wants to reset.
    try {
        require_once '../Resources/PHP/dbConnection.php';
        require_once '../Resources/PHP/sessionLogic.php';
        restrictAccess(8, $GLOBALS['role']);
    }
    catch (Exception $e) {
        die($e);
    }
    
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <script>
            var resetSuccessful = false;
            function allElementsLoaded() {
                if (resetSuccessful) {
                    //document.getElementById("frmToLogin").submit();
                }
            }
        </script>
    </head>
    <body onload="allElementsLoaded()">
        <form action="../LoginView/login.php" method="POST" id="frmToLogin">
            <input type="hidden" name="message" value="Database Successfully Reset.">
        </form>
        <?php
            try {
                // Connect to the database using the authentication parameters defined below
                $mysql_host = "localhost";
                $mysql_database = "Centres";
                $mysql_user = "scott";
                $mysql_password = "tiger";
                $dbh= new PDO("mysql:host=$mysql_host;dbname=$mysql_database", $mysql_user, $mysql_password);
                $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
                
                // Recreate/Update the database tables strucuture
                $query = file_get_contents("CentResCreateDB.sql");
                $dbh->exec($query);
                print("<h1>☑ Database Structure Updated</h1>");
                
                // Recreate/Update the database functions and procedures
                $query = file_get_contents("CentResDBFunctionsAndMethods.sql");
                $dbh->exec($query);
                print("<h1>☑ Database Functions Updated</h1>");

                // Recreate/Update the table 'tableshapes' records
                $query = file_get_contents("CentResPopulateTableshapesAndTables.sql");
                $dbh->exec($query);
                print("<h1>☑ Populated Tables</h1>");

                // Populate Menu
                $query = file_get_contents("CentResPopulateMenuWithAssociations.sql"); // Populate not Popuolate
                $dbh->exec($query);
                
                print("<h1>☑ Loaded Menu as to Database</h1>");

                // Message to verify the success of all queries in the block
                echo("<script>resetSuccessful = true;</script>");

                //echo("<script>location.replace('../CreateAdminView/CreateAdminView.php');</script>");

            // If one of the queries in the try block fail, the rest are not run. Message below will appear
            } catch(Exception $e) {
                print("<h1>☒ " .$e->getMessage(). "</h1>");
            }
    ?>
    </body>
</html>