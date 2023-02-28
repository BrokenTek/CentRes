<html>
    <head>
        <script>
            var resetSuccessful = false;
            function allElementsLoaded() {
                if (resetSuccessful) {
                    document.getElementById("frmToLogin").submit();
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
                $mysql_host = "localhost";
                $mysql_database = "Centres";
                $mysql_user = "scott";
                $mysql_password = "tiger";
                $dbh= new PDO("mysql:host=$mysql_host;dbname=$mysql_database", $mysql_user, $mysql_password);
                $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
                
                $query = file_get_contents("CentResCreateDB.txt");
                $dbh->exec($query);
                print("<h1>☑ Database Structure Updated</h1>");
                
                
                $query = file_get_contents("CentResDBFunctionsAndMethods.txt");
                $dbh->exec($query);
                print("<h1>☑ Database Functions Updated</h1>");

                $query = file_get_contents("CentResPopulate_tableshapes.txt");
                $dbh->exec($query);
                print("<h1>☑ Populated Tables</h1>");

                $query = file_get_contents("LoadThreeStooges.txt");
                $dbh->exec($query);
                print("<h1>☑ Loaded 3 Stooges as Employees</h1>");

                echo("<script>resetSuccessful = true;</script>");

            } catch(Exception $e) {
                print("<h1>☒ " .$e->getMessage(). "</h1>");//Remove or change message in production code
            }
    ?>
    </body>
</html>