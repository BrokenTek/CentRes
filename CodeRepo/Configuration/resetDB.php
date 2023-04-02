<!-- (to be removed) SUGGESTION: Comments added to the JS
    For the discovery of comments that are to be removed upon
    the conclusion of integration testing (or before), use the
    find in folder functionality (CTRL+Shift+F) to find all occurances 
    in all files in a folder of the text string: (to be removed) 
    **use this on CentRes folder to search all**  -->
<?php
    // makes sure the reset script is accessible if there is an error with the database,
    // there are no employees in the database, or a manager is logged in and wants to reset.
    try {
        //require_once '../Resources/php/connect_disconnect.php';
        //require_once '../Resources/php/sessionLogic.php';
        //restrictAccess(8, $GLOBALS['role']);
    }
    catch (Exception $e) {
        die($e);
    }
    
?>
<html>
    <head>
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
        <form action="../CodeRepo/LoginView/login.php" method="POST" id="frmToLogin">
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

                // Recreate/Update employees and related records
                $query = file_get_contents("LoadThreeStooges.sql");
                $dbh->exec($query);
                print("<h1>☑ Loaded 3 Stooges as Employees</h1>");

                // Populate Menu
                $query = file_get_contents("CentResPopulateMenuWithAssociations.sql"); // Populate not Popuolate
                $dbh->exec($query);
                //include 'menuPop.php';
                print("<h1>☑ Loaded Menu as to Database</h1>");

                // Message to verify the success of all queries in the block
                echo("<script>resetSuccessful = true;</script>");

                echo("<script>location.replace('../CreateAdminView/CreateAdminView.php');</script>");

            // If one of the queries in the try block fail, the rest are not run. Message below will appear
            } catch(Exception $e) {
                print("<h1>☒ " .$e->getMessage(). "</h1>"); // (to be removed) Remove or change message in production code
            }
    ?>
    </body>
</html>